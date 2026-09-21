<?php

namespace App\Database;

use PDO;

class Migrator
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::get();
    }

    public function runMigrations(string $migrationsDir): array
    {
        $results = [];
        $files = glob(rtrim($migrationsDir, '/\\') . '/*.sql');
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);
            $sql = file_get_contents($file);

            // Divide as queries respeitando separadores
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                fn($stmt) => !empty($stmt)
            );

            foreach ($statements as $stmt) {
                $this->pdo->exec($stmt);
            }

            $results[] = "Migração executada: {$filename}";
        }

        return $results;
    }

    public function runSeeds(string $seedsDir): array
    {
        $results = [];
        $files = glob(rtrim($seedsDir, '/\\') . '/*.sql');
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);
            $sql = file_get_contents($file);

            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                fn($stmt) => !empty($stmt)
            );

            foreach ($statements as $stmt) {
                $this->pdo->exec($stmt);
            }

            $results[] = "Seed executado: {$filename}";
        }

        return $results;
    }
}

