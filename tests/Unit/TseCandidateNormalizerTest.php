<?php

namespace Tests\Unit;

use Importer\Normalizers\TseCandidateNormalizer;
use PHPUnit\Framework\TestCase;

class TseCandidateNormalizerTest extends TestCase
{
    private TseCandidateNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new TseCandidateNormalizer();
    }

    public function testNormalizeRawTseRow(): void
    {
        $raw = [
            'ANO_ELEICAO' => '2026',
            'SG_UF' => 'SP',
            'DS_CARGO' => 'GOVERNADOR',
            'SQ_CANDIDATO' => '250001001001',
            'NR_CANDIDATO' => '10',
            'NM_CANDIDATO' => 'MARCOS AURELIO DE SOUZA',
            'NM_URNA_CANDIDATO' => 'MARCOS SOUZA',
            'SG_PARTIDO' => 'REPUBLICANOS',
            'DS_SITUACAO_CANDIDATURA' => 'DEFERIDO',
            'NM_COLIGACAO' => '#NULO#',
            'DT_ULTIMA_ATUALIZACAO' => '2026-09-01 10:00:00'
        ];

        $normalized = $this->normalizer->normalize($raw);

        $this->assertEquals(2026, $normalized['ano_eleicao']);
        $this->assertEquals('SP', $normalized['state_code']);
        $this->assertEquals('GOVERNADOR', $normalized['office_code']);
        $this->assertEquals('250001001001', $normalized['tse_id']);
        $this->assertEquals('10', $normalized['ballot_number']);
        $this->assertEquals('MARCOS SOUZA', $normalized['ballot_name']);
        $this->assertEquals('MARCOS AURELIO DE SOUZA', $normalized['full_name']);
        $this->assertEquals('REPUBLICANOS', $normalized['party_acronym']);
        $this->assertNull($normalized['federation_name']);
        $this->assertStringContainsString('250001001001', $normalized['photo_url']);
    }

    public function testMapsFederalDeputyOfficeCode(): void
    {
        $raw = [
            'ANO_ELEICAO' => '2026',
            'SG_UF' => 'SP',
            'DS_CARGO' => 'DEPUTADO FEDERAL',
            'SQ_CANDIDATO' => '250001003001',
            'NR_CANDIDATO' => '1010',
            'NM_CANDIDATO' => 'FERNANDO DIAS SANTOS',
            'NM_URNA_CANDIDATO' => 'FERNANDO DIAS',
            'SG_PARTIDO' => 'REPUBLICANOS'
        ];

        $normalized = $this->normalizer->normalize($raw);
        $this->assertEquals('DEPUTADO_FEDERAL', $normalized['office_code']);
    }
}

