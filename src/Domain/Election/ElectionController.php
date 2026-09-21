<?php

namespace App\Domain\Election;

use App\Database\Connection;
use App\Http\Request;
use App\Http\Response;

class ElectionController
{
    public function index(Request $request): void
    {
        $pdo = Connection::get();
        $stmt = $pdo->query('SELECT id, year, name, election_type, status, created_at FROM elections ORDER BY year DESC');
        $elections = $stmt->fetchAll();

        Response::success($elections);
    }

    public function show(Request $request, string $id): void
    {
        $pdo = Connection::get();
        $stmt = $pdo->prepare('SELECT id, year, name, election_type, status, created_at FROM elections WHERE id = :id');
        $stmt->execute(['id' => (int)$id]);
        $election = $stmt->fetch();

        if (!$election) {
            Response::error('ELECTION_NOT_FOUND', 'Eleição não encontrada.', [], 404);
            return;
        }

        Response::success($election);
    }

    public function offices(Request $request): void
    {
        $pdo = Connection::get();
        $stmt = $pdo->query('SELECT id, code, name, level FROM offices ORDER BY id ASC');
        $offices = $stmt->fetchAll();

        Response::success($offices);
    }

    public function parties(Request $request): void
    {
        $pdo = Connection::get();
        $stmt = $pdo->query('SELECT id, acronym, name, number FROM parties ORDER BY acronym ASC');
        $parties = $stmt->fetchAll();

        Response::success($parties);
    }
}

