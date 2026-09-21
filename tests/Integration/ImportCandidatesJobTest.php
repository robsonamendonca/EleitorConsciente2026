<?php

namespace Tests\Integration;

use App\Database\Connection;
use Importer\Adapters\TseOpenDataAdapter;
use Importer\Jobs\ImportCandidatesJob;
use PHPUnit\Framework\TestCase;

class ImportCandidatesJobTest extends TestCase
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

    public function testImportsFixtureCandidatesSuccessfully(): void
    {
        if (!$this->pdo) {
            return;
        }

        $fixturePath = __DIR__ . '/../../database/fixtures/tse_candidatos_sp_2026_sample.csv';
        $this->assertFileExists($fixturePath);

        $adapter = new TseOpenDataAdapter();
        $job = new ImportCandidatesJob($adapter, $this->pdo);

        $result = $job->execute($fixturePath, 'Teste Importação 2026');

        $this->assertContains($result['status'], ['completed', 'partial']);
        $this->assertGreaterThan(0, $result['records_processed']);
        $this->assertNotEmpty($result['file_hash']);

        // Teste de Idempotência: rodar novamente não deve falhar
        $secondRun = $job->execute($fixturePath, 'Teste Idempotência');
        $this->assertContains($secondRun['status'], ['completed', 'partial']);
        $this->assertEquals(0, $secondRun['records_inserted']);
        $this->assertGreaterThan(0, $secondRun['records_updated']);
    }
}

