# Guia de Implantação e Deploy - Eleitor Consciente 2026

## 1. Implantação com Docker (Ambiente Recomendado)

### Pré-requisitos:
- Docker Engine e Docker Compose instalados.

### Passos de Execução:
1. Copie o arquivo de variáveis de ambiente:
   ```bash
   cp .env.example .env
   ```
2. Inicie os contêineres:
   ```bash
   docker compose up -d --build
   ```
   *(No Windows, você também pode simplesmente dar duplo clique em `start.bat`)*
3. Execute as migrações e seeds iniciais:
   ```bash
   docker compose exec php php importer/cli.php migrate
   docker compose exec php php importer/cli.php seed
   ```
4. Execute a importação da base de candidatos:
   ```bash
   docker compose exec php php importer/cli.php import
   ```
5. Acesse a aplicação em `http://localhost:8080/`.

---

## 2. Implantação em Hospedagem Compartilhada (cPanel / Apache / Nginx)

1. Aponte o **DocumentRoot** do domínio ou vhost para a pasta `public/`.
2. Configure as extensões PHP necessárias: `pdo`, `pdo_mysql`, `mbstring`.
3. Importe os arquivos SQL `database/migrations/001_create_schema.sql` e `database/seeds/001_initial_data.sql` no phpMyAdmin ou cliente MySQL.
4. Ajuste as credenciais no arquivo `.env` na raiz do projeto.
5. Para automatizar importações periódicas, configure uma tarefa Cron executando:
   ```bash
   /usr/bin/php /caminho/para/importer/cli.php import
   ```

