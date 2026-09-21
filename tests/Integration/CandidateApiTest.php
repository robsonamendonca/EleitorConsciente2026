<?php

namespace Tests\Integration;

use App\Database\Connection;
use App\Domain\Candidate\CandidateRepository;
use PHPUnit\Framework\TestCase;

class CandidateApiTest extends TestCase
{
    private ?\PDO $pdo = null;

    protected function setUp(): void
    {
        try {
            $this->pdo = Connection::get();
        } catch (\Throwable $e) {
            $this->markTestSkipped('Banco de dados indisponível no ambiente de teste.');
        }
    }

    public function testFindPaginatedCandidates(): void
    {
        if (!$this->pdo) {
            return;
        }

        $repo = new CandidateRepository($this->pdo);
        $result = $repo->findPaginated(['page' => 1, 'per_page' => 10]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertIsArray($result['data']);
        $this->assertEquals(1, $result['meta']['page']);
        $this->assertEquals(10, $result['meta']['per_page']);
    }
}

