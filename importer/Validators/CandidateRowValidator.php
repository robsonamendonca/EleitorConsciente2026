<?php

namespace Importer\Validators;

class CandidateRowValidator
{
    public function validate(array $row): array
    {
        $errors = [];

        if (empty($row['ano_eleicao']) || !is_numeric($row['ano_eleicao'])) {
            $errors[] = ['field' => 'ano_eleicao', 'code' => 'INVALID_YEAR', 'message' => 'Ano da eleição ausente ou inválido.'];
        }

        if (empty($row['tse_id'])) {
            $errors[] = ['field' => 'tse_id', 'code' => 'MISSING_TSE_ID', 'message' => 'Identificador SQ_CANDIDATO (tse_id) é obrigatório.'];
        }

        if (empty($row['ballot_number']) || !preg_match('/^[0-9]+$/', $row['ballot_number'])) {
            $errors[] = ['field' => 'ballot_number', 'code' => 'INVALID_BALLOT_NUMBER', 'message' => 'Número de urna deve conter apenas dígitos numéricos.'];
        }

        if (empty($row['ballot_name'])) {
            $errors[] = ['field' => 'ballot_name', 'code' => 'MISSING_BALLOT_NAME', 'message' => 'Nome de urna é obrigatório.'];
        }

        if (empty($row['full_name'])) {
            $errors[] = ['field' => 'full_name', 'code' => 'MISSING_FULL_NAME', 'message' => 'Nome completo civil é obrigatório.'];
        }

        if (empty($row['party_acronym'])) {
            $errors[] = ['field' => 'party_acronym', 'code' => 'MISSING_PARTY', 'message' => 'Sigla do partido é obrigatória.'];
        }

        if (empty($row['office_code'])) {
            $errors[] = ['field' => 'office_code', 'code' => 'MISSING_OFFICE', 'message' => 'Código ou nome do cargo é obrigatório.'];
        }

        if (empty($row['state_code'])) {
            $errors[] = ['field' => 'state_code', 'code' => 'MISSING_STATE', 'message' => 'Sigla do estado (UF) é obrigatória.'];
        }

        return $errors;
    }
}

