<?php

namespace App\Domain\Import;

use App\Config\Env;
use App\Database\Connection;
use App\Http\Request;
use App\Http\Response;
use Importer\Adapters\TseOpenDataAdapter;
use Importer\Jobs\ImportCandidatesJob;

class ImportController
{
    private function checkAuth(Request $request): void
    {
        $expectedKey = Env::get('ADMIN_API_KEY');
        if (empty($expectedKey)) {
            Response::error('UNAUTHORIZED', 'Acesso administrativo desativado.', [], 401);
            return;
        }

        $authHeader = $request->header('Authorization');
        $token = null;
        if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
        } else {
            $token = $request->query('api_key') ?? $request->post('api_key');
        }

        if (!$token || !hash_equals($expectedKey, $token)) {
            Response::error('UNAUTHORIZED', 'Chave de autorização administrativa inválida.', [], 401);
            return;
        }
    }

    public function status(Request $request): void
    {
        $pdo = Connection::get();
        $stmt = $pdo->query("
            SELECT id, dataset_name, file_name, started_at, finished_at,
                   records_processed, records_inserted, records_updated, records_rejected, status
            FROM data_imports
            ORDER BY id DESC
            LIMIT 10
        ");
        Response::success($stmt->fetchAll());
    }

    public function adminImports(Request $request): void
    {
        $this->checkAuth($request);

        $pdo = Connection::get();
        $stmt = $pdo->query("SELECT * FROM data_imports ORDER BY id DESC LIMIT 50");
        Response::success($stmt->fetchAll());
    }

    public function adminImportDetail(Request $request, string $id): void
    {
        $this->checkAuth($request);

        $pdo = Connection::get();
        $stmt = $pdo->prepare("SELECT * FROM data_imports WHERE id = :id");
        $stmt->execute(['id' => (int)$id]);
        $import = $stmt->fetch();

        if (!$import) {
            Response::error('NOT_FOUND', 'Registro de importação não encontrado.', [], 404);
            return;
        }

        $stmtErrors = $pdo->prepare("SELECT * FROM import_errors WHERE data_import_id = :id ORDER BY id ASC LIMIT 100");
        $stmtErrors->execute(['id' => (int)$id]);
        $import['errors'] = $stmtErrors->fetchAll();

        Response::success($import);
    }

    public function adminTriggerImport(Request $request): void
    {
        $this->checkAuth($request);

        $filePath = $request->post('file_path') ?? (__DIR__ . '/../../../database/fixtures/tse_candidatos_sp_2026_sample.csv');
        $datasetName = $request->post('dataset_name') ?? 'Candidatos 2026 SP';

        if (!file_exists($filePath)) {
            Response::error('FILE_NOT_FOUND', "Arquivo não encontrado: {$filePath}", [], 400);
            return;
        }

        $adapter = new TseOpenDataAdapter();
        $job = new ImportCandidatesJob($adapter);
        $result = $job->execute($filePath, $datasetName);

        Response::success($result);
    }

    public function adminImportErrors(Request $request): void
    {
        $this->checkAuth($request);

        $pdo = Connection::get();
        $stmt = $pdo->query("SELECT * FROM import_errors ORDER BY id DESC LIMIT 100");
        Response::success($stmt->fetchAll());
    }
}

