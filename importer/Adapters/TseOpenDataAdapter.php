<?php

namespace Importer\Adapters;

use Importer\Contracts\DataSourceAdapterInterface;
use Importer\Normalizers\TseCandidateNormalizer;
use Importer\Validators\CandidateRowValidator;
use RuntimeException;

class TseOpenDataAdapter implements DataSourceAdapterInterface
{
    private TseCandidateNormalizer $normalizer;
    private CandidateRowValidator $validator;

    public function __construct()
    {
        $this->normalizer = new TseCandidateNormalizer();
        $this->validator = new CandidateRowValidator();
    }

    public function getSourceName(): string
    {
        return 'Portal de Dados Abertos do TSE';
    }

    public function discover(): array
    {
        return [
            [
                'id' => 'consulta_cand_2026_SP',
                'name' => 'Candidatos 2026 - São Paulo',
                'url' => 'https://dadosabertos.tse.jus.br/dataset/candidatos-2026',
                'format' => 'csv'
            ]
        ];
    }

    public function download(array $resource): string
    {
        $path = $resource['path'] ?? null;
        if (!$path || !file_exists($path)) {
            throw new RuntimeException("Arquivo de dados não encontrado localmente.");
        }
        return $path;
    }

    public function parse(string $filePath): iterable
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new RuntimeException("Arquivo CSV inacessível: {$filePath}");
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new RuntimeException("Não foi possível abrir o arquivo {$filePath}");
        }

        // Lê a primeira linha para identificar delimitador e cabeçalho
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return;
        }

        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        // Trata encoding ISO-8859-1 para UTF-8 se necessário
        if (!mb_check_encoding($firstLine, 'UTF-8')) {
            $firstLine = mb_convert_encoding($firstLine, 'UTF-8', 'ISO-8859-1');
        }

        $headers = str_getcsv(trim($firstLine), $delimiter, '"');
        $headers = array_map(fn($h) => trim($h, " \t\n\r\0\x0B\xEF\xBB\xBF"), $headers);

        $lineNum = 1;
        while (($line = fgets($handle)) !== false) {
            $lineNum++;
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (!mb_check_encoding($line, 'UTF-8')) {
                $line = mb_convert_encoding($line, 'UTF-8', 'ISO-8859-1');
            }

            $values = str_getcsv($line, $delimiter, '"');
            if (count($values) !== count($headers)) {
                // Linha com número incorreto de colunas
                continue;
            }

            $rawRow = array_combine($headers, $values);
            $rawRow['_line_number'] = $lineNum;

            yield $rawRow;
        }

        fclose($handle);
    }

    public function normalize(array $row): array
    {
        return $this->normalizer->normalize($row);
    }

    public function validate(array $row): array
    {
        return $this->validator->validate($row);
    }
}

