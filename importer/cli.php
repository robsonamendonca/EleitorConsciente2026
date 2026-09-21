<?php

// Script CLI para operações de banco e importação

require_once __DIR__ . '/../src/bootstrap.php';

use App\Config\Env;
use App\Database\Migrator;
use Importer\Adapters\TseOpenDataAdapter;
use Importer\Jobs\ImportCandidatesJob;

Env::load();

$action = $argv[1] ?? 'help';

switch ($action) {
    case 'migrate':
        echo "=== Executando Migrações ===\n";
        $migrator = new Migrator();
        $res = $migrator->runMigrations(__DIR__ . '/../database/migrations');
        foreach ($res as $msg) {
            echo "  [OK] {$msg}\n";
        }
        echo "Migrações concluídas com sucesso.\n";
        break;

    case 'seed':
        echo "=== Executando Seeds ===\n";
        $migrator = new Migrator();
        $res = $migrator->runSeeds(__DIR__ . '/../database/seeds');
        foreach ($res as $msg) {
            echo "  [OK] {$msg}\n";
        }
        echo "Seeds concluídos com sucesso.\n";
        break;

    case 'import':
        $filePath = __DIR__ . '/../database/fixtures/tse_candidatos_sp_2026_sample.csv';
        $datasetName = 'Candidatos 2026';
        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--file=')) {
                $filePath = substr($arg, 7);
            }
            if (str_starts_with($arg, '--dataset=')) {
                $datasetName = substr($arg, 10);
            }
        }

        echo "=== Iniciando Importação de Candidatos ===\n";
        echo "Arquivo: {$filePath}\n";
        echo "Dataset: {$datasetName}\n";

        $adapter = new TseOpenDataAdapter();
        $job = new ImportCandidatesJob($adapter);
        $result = $job->execute($filePath, $datasetName);

        echo "Status final: {$result['status']}\n";
        echo "Processados:  {$result['records_processed']}\n";
        echo "Inseridos:    {$result['records_inserted']}\n";
        echo "Atualizados:  {$result['records_updated']}\n";
        echo "Rejeitados:   {$result['records_rejected']}\n";
        echo "Hash:         {$result['file_hash']}\n";
        break;

    case 'import-all':
        $fixturesDir = __DIR__ . '/../database/fixtures';
        $files = glob($fixturesDir . '/tse_candidatos_*.csv');
        sort($files);

        if (empty($files)) {
            echo "Nenhum arquivo de candidatos encontrado em {$fixturesDir}\n";
            break;
        }

        echo "=== Importação em Lote ===\n";
        echo "Encontrados " . count($files) . " arquivo(s)\n\n";

        $adapter = new TseOpenDataAdapter();
        $job = new ImportCandidatesJob($adapter);
        $totalInserted = 0;
        $totalUpdated = 0;
        $totalRejected = 0;

        foreach ($files as $file) {
            $filename = basename($file);
            echo "Importando: {$filename}\n";

            // Detecta dataset name baseado no nome do arquivo
            $datasetName = 'Candidatos 2026';
            if (str_contains($filename, '_sp_')) {
                $datasetName = 'Candidatos 2026 - São Paulo';
            } elseif (str_contains($filename, '_rj_')) {
                $datasetName = 'Candidatos 2026 - Rio de Janeiro';
            }

            $result = $job->execute($file, $datasetName);
            echo "  Status: {$result['status']} | Inseridos: {$result['records_inserted']} | Atualizados: {$result['records_updated']} | Rejeitados: {$result['records_rejected']}\n\n";

            $totalInserted += $result['records_inserted'];
            $totalUpdated += $result['records_updated'];
            $totalRejected += $result['records_rejected'];
        }

        echo "=== Resumo Final ===\n";
        echo "Total Inseridos:   {$totalInserted}\n";
        echo "Total Atualizados: {$totalUpdated}\n";
        echo "Total Rejeitados:  {$totalRejected}\n";
        break;

    case 'status':
        $pdo = \App\Database\Connection::get();
        $stmt = $pdo->query("SELECT * FROM data_imports ORDER BY id DESC LIMIT 5");
        $rows = $stmt->fetchAll();
        echo "=== Últimas 5 Importações ===\n";
        foreach ($rows as $r) {
            echo "ID: {$r['id']} | Data: {$r['started_at']} | Status: {$r['status']} | Processados: {$r['records_processed']} | Inseridos: {$r['records_inserted']} | Erros: {$r['records_rejected']}\n";
        }
        break;

    case 'stats':
        $pdo = \App\Database\Connection::get();

        echo "=== Estatísticas dos Candidatos ===\n\n";

        // Total geral
        $total = $pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();
        echo "Total de candidatos: {$total}\n\n";

        if ($total == 0) {
            echo "Nenhum candidato importado.\n";
            echo "Execute: php importer/cli.php info\n";
            break;
        }

        // Por estado
        echo "Por Estado (UF):\n";
        $stmt = $pdo->query("SELECT state_code, COUNT(*) as total FROM candidates GROUP BY state_code ORDER BY state_code");
        while ($row = $stmt->fetch()) {
            echo "  {$row['state_code']}: {$row['total']}\n";
        }
        echo "\n";

        // Por cargo
        echo "Resumo de Cargos:\n";
        echo str_pad("Cargo", 25) . str_pad("Quantidade", 12) . "\n";
        echo str_repeat("-", 37) . "\n";

        $stmt = $pdo->query("
            SELECT o.name as office_name, COUNT(*) as total
            FROM candidates c
            JOIN offices o ON c.office_id = o.id
            GROUP BY o.id, o.name
            ORDER BY o.name
        ");
        $grandTotal = 0;
        while ($row = $stmt->fetch()) {
            $grandTotal += $row['total'];
            echo str_pad($row['office_name'], 25) . str_pad(number_format($row['total'], 0, ',', '.'), 12) . "\n";
        }
        echo str_repeat("-", 37) . "\n";
        echo str_pad("Total", 25) . str_pad(number_format($grandTotal, 0, ',', '.'), 12) . "\n";
        echo "\n";

        // Por partido
        echo "Por Partido (Top 15):\n";
        $stmt = $pdo->query("
            SELECT party_acronym, COUNT(*) as total
            FROM candidates
            GROUP BY party_acronym
            ORDER BY total DESC
            LIMIT 15
        ");
        while ($row = $stmt->fetch()) {
            echo "  {$row['party_acronym']}: {$row['total']}\n";
        }
        echo "\n";

        // Por situação
        echo "Por Situação da Candidatura:\n";
        $stmt = $pdo->query("
            SELECT registration_status, COUNT(*) as total
            FROM candidates
            GROUP BY registration_status
            ORDER BY total DESC
        ");
        while ($row = $stmt->fetch()) {
            echo "  {$row['registration_status']}: {$row['total']}\n";
        }
        break;

    case 'info':
        echo "=== Como Obter Dados Reais do TSE ===\n\n";
        echo "O projeto utiliza dados oficiais do Tribunal Superior Eleitoral (TSE).\n";
        echo "Os dados NÃO são incluídos no repositório por questões de direitos autorais.\n\n";
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ARQUIVO 1: CANDIDATOS ESTADUAIS (São Paulo)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "  Cargos: Governador, Vice-Governador, Senador, Suplentes,\n";
        echo "          Deputado Federal, Deputado Estadual\n\n";
        echo "  PASSO 1: Acesse o portal de dados abertos do TSE\n";
        echo "    https://dadosabertos.tse.jus.br/dataset/candidatos-2026\n\n";
        echo "  PASSO 2: Baixe o arquivo CSV de candidatos de São Paulo\n";
        echo "    Procure: consulta_cand_2026_SP.csv\n";
        echo "    (ou similar com dados de 2026)\n\n";
        echo "  PASSO 3: Coloque o arquivo na pasta fixtures/\n";
        echo "    database/fixtures/tse_candidatos_sp_2026.csv\n\n";
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "ARQUIVO 2: CANDIDATOS NACIONAIS (Presidente e Vice)\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "  Cargos: Presidente, Vice-Presidente\n\n";
        echo "  PASSO 1: No mesmo portal, baixe o arquivo nacional\n";
        echo "    Procure: consulta_cand_2026_BR.csv\n";
        echo "    (ou consulta_cand_2026.csv com SG_UF=BR)\n\n";
        echo "  PASSO 2: Coloque o arquivo na pasta fixtures/\n";
        echo "    database/fixtures/tse_candidatos_br_2026.csv\n\n";
        echo "  NOTA: O importador aceita SG_UF=BR para presidentes.\n";
        echo "        O state_code será armazenado como 'BR'.\n\n";
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "IMPORTAÇÃO\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "  Opção A: Importar arquivos individualmente\n";
        echo "    php importer/cli.php import --file=database/fixtures/tse_candidatos_sp_2026.csv\n";
        echo "    php importer/cli.php import --file=database/fixtures/tse_candidatos_br_2026.csv\n\n";
        echo "  Opção B: Importar todos os CSVs de uma vez (recomendado)\n";
        echo "    php importer/cli.php import-all\n";
        echo "    (importa automaticamente todos os tse_candidatos_*.csv da pasta fixtures/)\n\n";
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "FLUXO COMPLETO\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "  1. php importer/cli.php migrate    # Criar tabelas\n";
        echo "  2. php importer/cli.php seed       # Carregar partidos/cargos\n";
        echo "  3. Baixar CSVs do TSE (manualmente no navegador)\n";
        echo "  4. php importer/cli.php import-all # Importar todos\n";
        echo "  5. php importer/cli.php stats      # Verificar resultado\n\n";
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "IMPORTANTE\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        echo "  - O arquivo deve estar no formato CSV com delimitador ';'\n";
        echo "  - Apenas candidatos DEFERIDO serão importados\n";
        echo "  - O sistema filtra automaticamente por eleição de 2026\n";
        echo "  - Presidentes ficam no arquivo BR (nacional)\n";
        echo "  - Governador, Senador, Deputados ficam no arquivo SP (estadual)\n\n";
        echo "Alternativa: Acesse o DivulgaCandContas\n";
        echo "  https://divulgacandcontas.tse.jus.br/\n";
        echo "  Clique em 'Candidatos' → 'São Paulo' → 'Consulta'\n";
        echo "  Exporte os dados em CSV\n";
        break;

    default:
        echo "Uso: php importer/cli.php [comando] [opções]\n\n";
        echo "Comandos:\n";
        echo "  migrate          Executa migrações do banco de dados\n";
        echo "  seed             Carrega dados iniciais (eleições, cargos, partidos, fontes)\n";
        echo "  import           Importa candidatos de um arquivo CSV\n";
        echo "  import-all       Importa todos os CSVs da pasta fixtures/\n";
        echo "  status           Exibe as últimas 5 importações realizadas\n";
        echo "  stats            Exibe estatísticas dos candidatos importados\n";
        echo "  info             Exibe instruções para obter dados reais do TSE\n";
        echo "  help             Exibe esta ajuda\n\n";
        echo "Opções para 'import':\n";
        echo "  --file=caminho   Caminho do arquivo CSV (padrão: fixtures/tse_candidatos_sp_2026_sample.csv)\n";
        echo "  --dataset=nome   Nome do dataset para auditoria\n\n";
        echo "Fluxo típico:\n";
        echo "  1. php importer/cli.php info       # Ver como obter dados\n";
        echo "  2. php importer/cli.php migrate    # Criar tabelas\n";
        echo "  3. php importer/cli.php seed       # Carregar partidos/cargos\n";
        echo "  4. php importer/cli.php import     # Importar candidatos\n";
        echo "  5. php importer/cli.php stats      # Verificar resultado\n\n";
        echo "Exemplos:\n";
        echo "  php importer/cli.php migrate\n";
        echo "  php importer/cli.php seed\n";
        echo "  php importer/cli.php import\n";
        echo "  php importer/cli.php import --file=/caminho/novo.csv --dataset='Candidatos RJ'\n";
        echo "  php importer/cli.php import-all\n";
        echo "  php importer/cli.php stats\n";
        break;
}
