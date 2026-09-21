<?php

namespace App\Domain\Candidate;

use App\Database\Connection;
use PDO;

class CandidateRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::get();
    }

    public function findPaginated(array $filters): array
    {
        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = min(50, max(1, (int)($filters['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;

        $conditions = ['1=1'];
        $params = [];

        if (!empty($filters['election_id'])) {
            $conditions[] = 'c.election_id = :election_id';
            $params['election_id'] = (int)$filters['election_id'];
        }

        if (!empty($filters['year'])) {
            $conditions[] = 'e.year = :year';
            $params['year'] = (int)$filters['year'];
        }

        if (!empty($filters['state_code']) && strtoupper(trim($filters['state_code'])) !== 'ALL') {
            $state = strtoupper(trim($filters['state_code']));
            $isPresident = (!empty($filters['office']) && (strtoupper(trim($filters['office'])) === 'PRESIDENTE' || $filters['office'] == 1));

            if ($isPresident) {
                // Presidente é cargo nacional (BR)
                $conditions[] = "(c.state_code = 'BR' OR c.state_code = :state_code)";
                $params['state_code'] = $state;
            } elseif ($state === 'BR') {
                $conditions[] = "c.state_code = 'BR'";
            } else {
                // Eleitor de SP vota em cargos estaduais de SP E cargos nacionais (Presidente)
                $conditions[] = "(c.state_code = :state_code OR c.state_code = 'BR')";
                $params['state_code'] = $state;
            }
        }

        if (!empty($filters['office'])) {
            if (is_numeric($filters['office'])) {
                $conditions[] = 'c.office_id = :office_id';
                $params['office_id'] = (int)$filters['office'];
            } else {
                $conditions[] = 'o.code = :office_code';
                $params['office_code'] = strtoupper(trim($filters['office']));
            }
        }

        if (!empty($filters['party'])) {
            $conditions[] = 'c.party_acronym = :party_acronym';
            $params['party_acronym'] = strtoupper(trim($filters['party']));
        }

        if (!empty($filters['status'])) {
            $conditions[] = 'c.registration_status = :status';
            $params['status'] = trim($filters['status']);
        }

        if (!empty($filters['ballot_number'])) {
            $conditions[] = 'c.ballot_number LIKE :ballot_number';
            $params['ballot_number'] = trim($filters['ballot_number']) . '%';
        }

        if (!empty($filters['name'])) {
            $conditions[] = '(c.ballot_name LIKE :name OR c.full_name LIKE :name_full)';
            $nameTerm = '%' . trim($filters['name']) . '%';
            $params['name'] = $nameTerm;
            $params['name_full'] = $nameTerm;
        }

        $whereClause = implode(' AND ', $conditions);

        // Contagem total
        $countSql = "SELECT COUNT(*) FROM candidates c 
                     JOIN elections e ON c.election_id = e.id 
                     JOIN offices o ON c.office_id = o.id 
                     WHERE {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Ordenação permitida
        $sortAllowed = [
            'ballot_name' => 'c.ballot_name ASC',
            'ballot_number' => 'c.ballot_number ASC',
            'party_acronym' => 'c.party_acronym ASC',
            'created_at' => 'c.created_at DESC'
        ];
        $sortKey = $filters['sort'] ?? 'ballot_name';
        $orderBy = $sortAllowed[$sortKey] ?? 'c.ballot_name ASC';

        $dataSql = "SELECT c.id, c.election_id, c.tse_id, c.office_id, c.state_code, c.municipality_code,
                           c.ballot_number, c.ballot_name, c.full_name, c.party_acronym, c.federation_name,
                           c.registration_status, c.photo_url, c.source_last_updated_at, c.created_at, c.updated_at,
                           o.name AS office_name, o.code AS office_code, o.level AS office_level,
                           e.year AS election_year, e.name AS election_name
                    FROM candidates c
                    JOIN elections e ON c.election_id = e.id
                    JOIN offices o ON c.office_id = o.id
                    WHERE {$whereClause}
                    ORDER BY {$orderBy}
                    LIMIT :limit OFFSET :offset";

        $dataStmt = $this->pdo->prepare($dataSql);
        foreach ($params as $k => $v) {
            $dataStmt->bindValue($k, $v);
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();

        $rows = $dataStmt->fetchAll();

        return [
            'data' => $rows,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ];
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT c.*, o.name AS office_name, o.code AS office_code, o.level AS office_level,
                       e.year AS election_year, e.name AS election_name,
                       p.name AS party_full_name, p.number AS party_number
                FROM candidates c
                JOIN elections e ON c.election_id = e.id
                JOIN offices o ON c.office_id = o.id
                LEFT JOIN parties p ON c.party_acronym = p.acronym
                WHERE c.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findSources(int $candidateId): array
    {
        $sql = "SELECT cs.*, s.name AS source_name, s.url AS source_url, s.source_type, s.authority_level
                FROM candidate_sources cs
                JOIN sources s ON cs.source_id = s.id
                WHERE cs.candidate_id = :id
                ORDER BY cs.retrieved_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $candidateId]);
        return $stmt->fetchAll();
    }

    public function findProposals(int $candidateId): array
    {
        $sql = "SELECT * FROM candidate_proposals WHERE candidate_id = :id ORDER BY category ASC, id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $candidateId]);
        return $stmt->fetchAll();
    }

    public function findRecords(int $candidateId): array
    {
        $sql = "SELECT * FROM candidate_records WHERE candidate_id = :id ORDER BY reference_date DESC, id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $candidateId]);
        return $stmt->fetchAll();
    }
}

