<?php

namespace App\Domain\Health;

use App\Database\Connection;
use App\Http\Request;
use App\Http\Response;
use PDOException;
use Throwable;

class HealthController
{
    public function check(Request $request): void
    {
        $dbStatus = 'ok';
        $lastImport = null;

        try {
            $pdo = Connection::get();
            $pdo->query('SELECT 1');

            $stmt = $pdo->query('SELECT status, finished_at, dataset_name FROM data_imports ORDER BY id DESC LIMIT 1');
            $row = $stmt->fetch();
            if ($row) {
                $lastImport = [
                    'dataset' => $row['dataset_name'],
                    'status' => $row['status'],
                    'finished_at' => $row['finished_at']
                ];
            }
        } catch (Throwable $e) {
            $dbStatus = 'error';
        }

        Response::success([
            'application' => 'ok',
            'database' => $dbStatus,
            'timestamp' => date('c'),
            'version' => '1.0.0',
            'last_import' => $lastImport
        ]);
    }
}

