<?php

namespace Importer\Jobs;

use App\Database\Connection;
use Importer\Contracts\DataSourceAdapterInterface;
use PDO;
use Throwable;

class ImportCandidatesJob
{
    private PDO $pdo;
    private DataSourceAdapterInterface $adapter;

    public function __construct(DataSourceAdapterInterface $adapter, ?PDO $pdo = null)
    {
        $this->adapter = $adapter;
        $this->pdo = $pdo ?? Connection::get();
    }

    public function execute(string $filePath, string $datasetName = 'Candidatos 2026', int $sourceId = 1): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Arquivo para importação não existe: {$filePath}");
        }

        $fileHash = hash_file('sha256', $filePath);
        $fileName = basename($filePath);
        $startedAt = date('Y-m-d H:i:s');

        // Cria registro de auditoria da importação
        $stmtImport = $this->pdo->prepare("
            INSERT INTO data_imports (dataset_name, source_id, source_url, file_name, file_hash, started_at, status)
            VALUES (:dataset, :source_id, :url, :filename, :hash, :started_at, 'running')
        ");
        $stmtImport->execute([
            'dataset' => $datasetName,
            'source_id' => $sourceId,
            'url' => 'https://dadosabertos.tse.jus.br/',
            'filename' => $fileName,
            'hash' => $fileHash,
            'started_at' => $startedAt
        ]);
        $importId = (int)$this->pdo->lastInsertId();
        \App\Support\Logger::info("Iniciando importação de candidatos", [
            'import_id' => $importId,
            'dataset' => $datasetName,
            'file' => $fileName,
            'hash' => $fileHash
        ], 'imports');

        $processed = 0;
        $inserted = 0;
        $updated = 0;
        $rejected = 0;

        try {
            // Mapeia cargos existentes no banco
            $officeStmt = $this->pdo->query("SELECT id, code FROM offices");
            $offices = [];
            while ($row = $officeStmt->fetch()) {
                $offices[strtoupper($row['code'])] = (int)$row['id'];
            }

            // Garante eleição de 2026
            $electionStmt = $this->pdo->prepare("SELECT id FROM elections WHERE year = :year LIMIT 1");
            $electionStmt->execute(['year' => 2026]);
            $electionId = (int)$electionStmt->fetchColumn();
            if (!$electionId) {
                $this->pdo->exec("INSERT INTO elections (year, name, election_type, status) VALUES (2026, 'Eleições Gerais 2026', 'GERAL', 'ativo')");
                $electionId = (int)$this->pdo->lastInsertId();
            }

            // Prepared statements para candidatos
            $findCandidateStmt = $this->pdo->prepare("SELECT id FROM candidates WHERE election_id = :election_id AND tse_id = :tse_id");
            
            $insertCandidateStmt = $this->pdo->prepare("
                INSERT INTO candidates (
                    election_id, tse_id, office_id, state_code, municipality_code,
                    ballot_number, ballot_name, full_name, party_acronym, federation_name,
                    registration_status, photo_url, source_last_updated_at
                ) VALUES (
                    :election_id, :tse_id, :office_id, :state_code, :municipality_code,
                    :ballot_number, :ballot_name, :full_name, :party_acronym, :federation_name,
                    :registration_status, :photo_url, :source_last_updated_at
                )
            ");

            $updateCandidateStmt = $this->pdo->prepare("
                UPDATE candidates SET
                    office_id = :office_id,
                    state_code = :state_code,
                    ballot_number = :ballot_number,
                    ballot_name = :ballot_name,
                    full_name = :full_name,
                    party_acronym = :party_acronym,
                    federation_name = :federation_name,
                    registration_status = :registration_status,
                    photo_url = :photo_url,
                    source_last_updated_at = :source_last_updated_at,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $insertSourceStmt = $this->pdo->prepare("
                INSERT INTO candidate_sources (candidate_id, source_id, source_reference, reference_date, retrieved_at, content_hash)
                VALUES (:candidate_id, :source_id, :source_ref, :ref_date, :retrieved_at, :content_hash)
            ");

            $insertErrorStmt = $this->pdo->prepare("
                INSERT INTO import_errors (data_import_id, row_reference, field_name, error_code, error_message, raw_value)
                VALUES (:import_id, :row_ref, :field_name, :error_code, :error_message, :raw_value)
            ");

            // Processa linha por linha via generator
            foreach ($this->adapter->parse($filePath) as $rawRow) {
                $processed++;
                $lineRef = "Linha " . ($rawRow['_line_number'] ?? $processed);

                $normalized = $this->adapter->normalize($rawRow);
                $errors = $this->adapter->validate($normalized);

                if (!empty($errors)) {
                    $rejected++;
                    foreach ($errors as $err) {
                        $insertErrorStmt->execute([
                            'import_id' => $importId,
                            'row_ref' => $lineRef,
                            'field_name' => $err['field'] ?? 'unknown',
                            'error_code' => $err['code'] ?? 'VALIDATION_ERROR',
                            'error_message' => $err['message'] ?? 'Erro de validação',
                            'raw_value' => json_encode($rawRow, JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                    continue;
                }

                // Obtém ID do cargo
                $officeCode = $normalized['office_code'];
                $officeId = $offices[$officeCode] ?? null;
                if (!$officeId) {
                    // Cadastra cargo caso novo
                    $insOffice = $this->pdo->prepare("INSERT INTO offices (code, name, level) VALUES (:code, :name, 'ESTADUAL')");
                    $insOffice->execute(['code' => $officeCode, 'name' => ucwords(strtolower(str_replace('_', ' ', $officeCode)))]);
                    $officeId = (int)$this->pdo->lastInsertId();
                    $offices[$officeCode] = $officeId;
                }

                // Verifica se já existe
                $findCandidateStmt->execute([
                    'election_id' => $electionId,
                    'tse_id' => $normalized['tse_id']
                ]);
                $existingId = $findCandidateStmt->fetchColumn();

                $candidateParams = [
                    'office_id' => $officeId,
                    'state_code' => $normalized['state_code'],
                    'ballot_number' => $normalized['ballot_number'],
                    'ballot_name' => $normalized['ballot_name'],
                    'full_name' => $normalized['full_name'],
                    'party_acronym' => $normalized['party_acronym'],
                    'federation_name' => $normalized['federation_name'],
                    'registration_status' => $normalized['registration_status'],
                    'photo_url' => $normalized['photo_url'],
                    'source_last_updated_at' => $normalized['source_last_updated_at']
                ];

                if ($existingId) {
                    $candidateId = (int)$existingId;
                    $candidateParams['id'] = $candidateId;
                    $updateCandidateStmt->execute($candidateParams);
                    $updated++;
                } else {
                    $candidateParams['election_id'] = $electionId;
                    $candidateParams['tse_id'] = $normalized['tse_id'];
                    $candidateParams['municipality_code'] = null;
                    $insertCandidateStmt->execute($candidateParams);
                    $candidateId = (int)$this->pdo->lastInsertId();
                    $inserted++;

                    // Registra fonte vinculada
                    $insertSourceStmt->execute([
                        'candidate_id' => $candidateId,
                        'source_id' => $sourceId,
                        'source_ref' => 'TSE Dados Abertos 2026',
                        'ref_date' => date('Y-m-d'),
                        'retrieved_at' => date('Y-m-d H:i:s'),
                        'content_hash' => $fileHash
                    ]);
                }
            }

            // Finaliza status da importação
            $finalStatus = ($rejected > 0 && ($inserted > 0 || $updated > 0)) ? 'partial' : (($rejected > 0) ? 'failed' : 'completed');
            $finishedAt = date('Y-m-d H:i:s');

            $stmtFinish = $this->pdo->prepare("
                UPDATE data_imports SET
                    status = :status,
                    finished_at = :finished_at,
                    records_processed = :processed,
                    records_inserted = :inserted,
                    records_updated = :updated,
                    records_rejected = :rejected
                WHERE id = :id
            ");
            $stmtFinish->execute([
                'status' => $finalStatus,
                'finished_at' => $finishedAt,
                'processed' => $processed,
                'inserted' => $inserted,
                'updated' => $updated,
                'rejected' => $rejected,
                'id' => $importId
            ]);

            \App\Support\Logger::info("Importação finalizada", [
                'import_id' => $importId,
                'status' => $finalStatus,
                'processed' => $processed,
                'inserted' => $inserted,
                'updated' => $updated,
                'rejected' => $rejected
            ], 'imports');

            return [
                'import_id' => $importId,
                'status' => $finalStatus,
                'file_hash' => $fileHash,
                'records_processed' => $processed,
                'records_inserted' => $inserted,
                'records_updated' => $updated,
                'records_rejected' => $rejected
            ];

        } catch (Throwable $e) {
            \App\Support\Logger::error("Falha crítica na importação: " . $e->getMessage(), [
                'import_id' => $importId,
                'file' => $fileName
            ], 'imports');

            $stmtFail = $this->pdo->prepare("
                UPDATE data_imports SET
                    status = 'failed',
                    finished_at = NOW(),
                    records_processed = :processed,
                    records_inserted = :inserted,
                    records_updated = :updated,
                    records_rejected = :rejected,
                    error_message = :err
                WHERE id = :id
            ");
            $stmtFail->execute([
                'processed' => $processed,
                'inserted' => $inserted,
                'updated' => $updated,
                'rejected' => $rejected,
                'err' => $e->getMessage(),
                'id' => $importId
            ]);

            throw $e;
        }
    }
}

