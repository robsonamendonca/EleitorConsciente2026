<?php

namespace Tests\Unit;

use Importer\Validators\CandidateRowValidator;
use PHPUnit\Framework\TestCase;

class CandidateRowValidatorTest extends TestCase
{
    private CandidateRowValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CandidateRowValidator();
    }

    public function testValidRowPassesValidation(): void
    {
        $row = [
            'ano_eleicao' => 2026,
            'tse_id' => '250001001001',
            'ballot_number' => '10',
            'ballot_name' => 'MARCOS SOUZA',
            'full_name' => 'MARCOS AURELIO DE SOUZA',
            'party_acronym' => 'REPUBLICANOS',
            'office_code' => 'GOVERNADOR',
            'state_code' => 'SP'
        ];

        $errors = $this->validator->validate($row);
        $this->assertEmpty($errors);
    }

    public function testMissingTseIdFails(): void
    {
        $row = [
            'ano_eleicao' => 2026,
            'tse_id' => '',
            'ballot_number' => '10',
            'ballot_name' => 'MARCOS SOUZA',
            'full_name' => 'MARCOS AURELIO DE SOUZA',
            'party_acronym' => 'REPUBLICANOS',
            'office_code' => 'GOVERNADOR',
            'state_code' => 'SP'
        ];

        $errors = $this->validator->validate($row);
        $this->assertNotEmpty($errors);
        $this->assertEquals('MISSING_TSE_ID', $errors[0]['code']);
    }

    public function testNonNumericBallotNumberFails(): void
    {
        $row = [
            'ano_eleicao' => 2026,
            'tse_id' => '250001001001',
            'ballot_number' => 'ABC10',
            'ballot_name' => 'MARCOS SOUZA',
            'full_name' => 'MARCOS AURELIO DE SOUZA',
            'party_acronym' => 'REPUBLICANOS',
            'office_code' => 'GOVERNADOR',
            'state_code' => 'SP'
        ];

        $errors = $this->validator->validate($row);
        $this->assertNotEmpty($errors);
        $this->assertEquals('INVALID_BALLOT_NUMBER', $errors[0]['code']);
    }

    public function testInvalidYearFails(): void
    {
        $row = [
            'ano_eleicao' => 'ANO_INVALIDO',
            'tse_id' => '250001001001',
            'ballot_number' => '10',
            'ballot_name' => 'MARCOS SOUZA',
            'full_name' => 'MARCOS AURELIO DE SOUZA',
            'party_acronym' => 'REPUBLICANOS',
            'office_code' => 'GOVERNADOR',
            'state_code' => 'SP'
        ];

        $errors = $this->validator->validate($row);
        $this->assertNotEmpty($errors);
        $this->assertEquals('INVALID_YEAR', $errors[0]['code']);
    }
}

