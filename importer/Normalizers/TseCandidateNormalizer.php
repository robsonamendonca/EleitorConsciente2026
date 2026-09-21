<?php

namespace Importer\Normalizers;

class TseCandidateNormalizer
{
    private array $officeMap = [
        '1' => 'PRESIDENTE',
        'PRESIDENTE' => 'PRESIDENTE',
        'PRESIDENTE DA REPÚBLICA' => 'PRESIDENTE',
        '2' => 'VICE_PRESIDENTE',
        'VICE_PRESIDENTE' => 'VICE_PRESIDENTE',
        'VICE-PRESIDENTE' => 'VICE_PRESIDENTE',
        'VICE PRESIDENTE' => 'VICE_PRESIDENTE',
        'VICE-PRESIDENTE DA REPÚBLICA' => 'VICE_PRESIDENTE',
        '3' => 'GOVERNADOR',
        'GOVERNADOR' => 'GOVERNADOR',
        'VICE_GOVERNADOR' => 'VICE_GOVERNADOR',
        'VICE-GOVERNADOR' => 'VICE_GOVERNADOR',
        'VICE GOVERNADOR' => 'VICE_GOVERNADOR',
        '5' => 'SENADOR',
        'SENADOR' => 'SENADOR',
        '1_SUPLENTE' => '1_SUPLENTE',
        '1º SUPLENTE' => '1_SUPLENTE',
        'PRIMEIRO SUPLENTE' => '1_SUPLENTE',
        '2_SUPLENTE' => '2_SUPLENTE',
        '2º SUPLENTE' => '2_SUPLENTE',
        'SEGUNDO SUPLENTE' => '2_SUPLENTE',
        '6' => 'DEPUTADO_FEDERAL',
        'DEPUTADO FEDERAL' => 'DEPUTADO_FEDERAL',
        '7' => 'DEPUTADO_ESTADUAL',
        'DEPUTADO ESTADUAL' => 'DEPUTADO_ESTADUAL'
    ];

    public function normalize(array $rawRow): array
    {
        // Normaliza chaves para maiúsculas e remove espaços
        $cleanRow = [];
        foreach ($rawRow as $key => $value) {
            $k = strtoupper(trim((string)$key));
            $cleanRow[$k] = $this->cleanValue($value);
        }

        $cargoRaw = $cleanRow['DS_CARGO'] ?? $cleanRow['CD_CARGO'] ?? '';
        $officeCode = $this->officeMap[strtoupper(trim($cargoRaw))] ?? strtoupper(str_replace(' ', '_', trim($cargoRaw)));

        $federation = $cleanRow['NM_COLIGACAO'] ?? $cleanRow['DS_COMPOSICAO_COLIGACAO'] ?? null;
        if ($federation === '' || $federation === '#NULO#') {
            $federation = null;
        }

        $photoUrl = null;
        $tseId = $cleanRow['SQ_CANDIDATO'] ?? '';
        $year = $cleanRow['ANO_ELEICAO'] ?? '2026';
        if (!empty($tseId)) {
            // Busca foto local: primeiro SP, depois BR (nacional)
            $stateCode = strtoupper(trim((string)($cleanRow['SG_UF'] ?? 'SP')));
            
            $found = false;
            
            // Tenta foto do estado (ex: FSP para SP, FBR para BR)
            $statePrefix = $stateCode === 'BR' ? 'FBR' : 'F' . $stateCode;
            $localPath = __DIR__ . '/../../database/foto_cand' . $year . '_' . $stateCode . '_div/' . $statePrefix . $tseId . '_div.jpg';
            if (file_exists($localPath)) {
                // BR usa /fotos_br/, estados usam /fotos/
                $photoUrl = ($stateCode === 'BR' ? '/fotos_br/' : '/fotos/') . $statePrefix . $tseId . '_div.jpg';
                $found = true;
            }
            
            // Se não encontrou no estado, tenta o diretório nacional (BR)
            if (!$found && $stateCode !== 'BR') {
                $brPath = __DIR__ . '/../../database/foto_cand' . $year . '_BR_div/FBR' . $tseId . '_div.jpg';
                if (file_exists($brPath)) {
                    $photoUrl = '/fotos_br/FBR' . $tseId . '_div.jpg';
                    $found = true;
                }
            }
            
            // Fallback: URL oficial do DivulgaCandContas
            if (!$found) {
                $photoUrl = "https://divulgacandcontas.tse.jus.br/divulga/rest/v1/candidatura/buscar/foto/{$year}/999/{$tseId}";
            }
        }

        $updatedAt = $cleanRow['DT_ULTIMA_ATUALIZACAO'] ?? date('Y-m-d H:i:s');
        if (str_contains($updatedAt, '/')) {
            // Converte formato DD/MM/AAAA para YYYY-MM-DD
            $d = \DateTime::createFromFormat('d/m/Y H:i:s', $updatedAt) ?: \DateTime::createFromFormat('d/m/Y', $updatedAt);
            if ($d) {
                $updatedAt = $d->format('Y-m-d H:i:s');
            }
        }

        return [
            'ano_eleicao' => (int)($cleanRow['ANO_ELEICAO'] ?? 2026),
            'tse_id' => (string)$tseId,
            'ballot_number' => (string)($cleanRow['NR_CANDIDATO'] ?? ''),
            'ballot_name' => (string)($cleanRow['NM_URNA_CANDIDATO'] ?? $cleanRow['NM_CANDIDATO'] ?? ''),
            'full_name' => (string)($cleanRow['NM_CANDIDATO'] ?? ''),
            'party_acronym' => strtoupper(trim((string)($cleanRow['SG_PARTIDO'] ?? ''))),
            'federation_name' => $federation,
            'office_code' => $officeCode,
            'state_code' => strtoupper(trim((string)($cleanRow['SG_UF'] ?? 'SP'))),
            'registration_status' => (string)($cleanRow['DS_SITUACAO_CANDIDATURA'] ?? 'DEFERIDO'),
            'photo_url' => $photoUrl,
            'source_last_updated_at' => $updatedAt
        ];
    }

    private function cleanValue(mixed $val): ?string
    {
        if ($val === null) {
            return null;
        }
        $str = trim((string)$val, " \t\n\r\0\x0B\"'");
        if ($str === '#NULO#' || $str === '#NE#' || $str === '-1' || $str === '-3' || $str === '-4') {
            return null;
        }
        return $str;
    }
}

