# Eleitor Consciente 2026

[![CI](https://github.com/robsonamendonca/EleitorConsciente2026/actions/workflows/ci.yml/badge.svg)](https://github.com/robsonamendonca/EleitorConsciente2026/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)](https://www.mysql.com/)
[![Security](https://img.shields.io/badge/Security-LGPD%20Compliant-green.svg)](SECURITY.md)
[![Contributions](https://img.shields.io/badge/Contributions-Welcome-brightgreen.svg)](CONTRIBUTING.md)

> **Plataforma publica, aberta, acessivel e neutra para consulta e organizacao eleitoral, com foco inicial no estado de Sao Paulo nas Eleicoes Gerais de 2026.**

O Eleitor Consciente resolve o problema de **acesso fragmentado e desinformativo** as informacoes eleitorais. Fornecemos dados oficiais do TSE de forma clara, neutra e auditavel, permitindo que cada eleitor tome decisoes informadas sem interferencia politica ou algoritmos de recomendacao.

---

## 🏛️ Princípios Fundamentais

1. **Neutralidade Estrita:** A aplicação apresenta dados verificáveis sem recomendar ou desaconselhar candidatos. A decisão de voto é exclusiva do eleitor.
2. **Transparência e Auditabilidade:** Todo candidato possui suas fontes governamentais e datas de coleta informadas. O importador calcula hashes SHA-256 e audita cada registro inserido, atualizado ou rejeitado.
3. **Privacidade por Padrão (LGPD):** Não solicitamos login, CPF ou dados pessoais. A "Cola Eleitoral" opera **100% no navegador (`localStorage`)** do usuário e nunca trafega nem é armazenada no servidor.
4. **Acessibilidade e Desempenho:** Interface mobile-first desenvolvida em HTML5 semântico, CSS3 moderno e Vanilla JavaScript, sem dependência de frameworks pesados.

---

## 🛡️ Seguranca e Privacidade

| Medida | Status |
|--------|--------|
| **Dados Pessoais** | Nao coletamos nome, CPF, email ou dados sensiveis |
| **Cola Eleitoral** | 100% local (localStorage), nunca trafega pela internet |
| **Credenciais** | Variaveis de ambiente, nunca hardcoded no codigo |
| **Historico Git** | Arquivos sensivel no .gitignore, sem secrets commitados |
| **Headers HTTP** | CSP, X-Frame-Options, X-XSS-Protection habilitados |
| **SQL Injection** | Prepared statements em todas as queries |
| **XSS** | Escape de saida HTML em todas as exibicoes |
| **Auditoria** | Hash SHA-256 em cada lote de dados importados |
| **Backup** | Automatizado com retencao configuravel |
| **LGPD** | 100% conforme - leia [SECURITY.md](SECURITY.md) |

### Gerar Chaves Seguras

```bash
# Gerar chave de administracao
openssl rand -hex 32

# Gerar senha forte para banco
openssl rand -base64 24
```

---

## 🚀 Início Rápido

### Pré-requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado e em execução
- Git instalado (para clonar o repositório)
- PowerShell, Terminal ou Prompt de Comando
- **Navegador web** (para baixar dados do TSE)

### Passo 1: Clone o Repositório

```bash
git clone https://github.com/robsonamendonca/EleitorConsciente2026.git
cd EleitorConsciente2026
```

### Passo 2: Inicie os Contêineres

**No Windows** (duplo clique):
```text
start.bat
```

**Ou no terminal:**
```powershell
docker compose up -d --build
```

### Passo 3: Configure o Banco de Dados

```bash
docker compose exec php php importer/cli.php migrate
docker compose exec php php importer/cli.php seed
```

### Passo 4: Obtenha os Dados Reais do TSE

**IMPORTANTE:** O projeto NÃO inclui dados fictícios. Você deve baixar os dados oficiais do TSE.

```bash
docker compose exec php php importer/cli.php info
```

Ou acesse diretamente: **https://dadosabertos.tse.jus.br/dataset/candidatos-2026**

Baixe **dois arquivos**:

| Arquivo | Cargos | Coloque em |
|---------|--------|------------|
| `consulta_cand_2026_SP.csv` | Governador, Senador, Deputados | `database/fixtures/tse_candidatos_sp_2026.csv` |
| `consulta_cand_2026_BR.csv` | Presidente, Vice-Presidente | `database/fixtures/tse_candidatos_br_2026.csv` |

### Passo 5: Importe os Candidatos

```bash
docker compose exec php php importer/cli.php import-all
```

Ou importe arquivo por arquivo:
```bash
docker compose exec php php importer/cli.php import --file=database/fixtures/tse_candidatos_sp_2026.csv
docker compose exec php php importer/cli.php import --file=database/fixtures/tse_candidatos_br_2026.csv
```

### Passo 6: Acesse a Aplicação

Abra no navegador: **http://localhost:8080**

---

## 📋 Guia Completo de Execução com Docker

O projeto é 100% turnkey e já inclui o contêiner PHP 8.2 pré-configurado com as extensões necessárias (`pdo_mysql`, `mod_rewrite`, `zip` e Composer) e MySQL 8.0.

### Iniciar os Contêineres

```powershell
docker compose up -d --build
```

Ou Execute `start.bat` (Windows).

### Parar os Contêineres

```powershell
docker compose down
```

Ou execute `stop.bat`.

### Parar e Limpar Dados do MySQL

```powershell
docker compose down -v
```

Ou execute `stop.bat clean`.

### Verificar Status dos Contêineres

```powershell
docker compose ps
```

### Ver Logs em Tempo Real

```powershell
docker compose logs -f php
docker compose logs -f mysql
```

---

## 🗄️ Inicialização do Banco e Importação de Candidatos

Com os contêineres iniciados, execute os comandos abaixo dentro do contêiner PHP:

### 1. Executar Migrações do Banco

```bash
docker compose exec php php importer/cli.php migrate
```

### 2. Carregar Seeds Iniciais

```bash
docker compose exec php php importer/cli.php seed
```

Carga de eleições, cargos oficiais, partidos e fontes.

### 3. Obter Dados Reais do TSE

```bash
docker compose exec php php importer/cli.php info
```

Ou acesse manualmente: **https://dadosabertos.tse.jus.br/dataset/candidatos-2026**

**IMPORTANTE:** O projeto não inclui dados fictícios. Você deve baixar os dados oficiais do TSE.

Baixe **dois arquivos**:
- `consulta_cand_2026_SP.csv` → `database/fixtures/tse_candidatos_sp_2026.csv`
- `consulta_cand_2026_BR.csv` → `database/fixtures/tse_candidatos_br_2026.csv`

### 4. Importar Candidatos

```bash
docker compose exec php php importer/cli.php import-all
```

Apenas candidatos com situação `DEFERIDO` para a eleição de 04/10/2026 são importados.

**Importação com arquivo personalizado:**
```bash
docker compose exec php php importer/cli.php import --file=caminho/do/arquivo.csv
```

### 5. Verificar Status das Importações

```bash
docker compose exec php php importer/cli.php status
```

### 6. Ver Estatísticas dos Candidatos

```bash
docker compose exec php php importer/cli.php stats
```

Exibe resumo por estado, cargo, partido e situação.

### 7. Importar Todos os CSVs (Lote)

```bash
docker compose exec php php importer/cli.php import-all
```

Importa automaticamente todos os arquivos `tse_candidatos_*.csv` da pasta `database/fixtures/`.

### 8. Acessar a Aplicação no Navegador

| Página | URL |
|--------|-----|
| Interface Web | http://localhost:8080/ |
| Consulta de Candidatos | http://localhost:8080/candidatos |
| Minha Cola Eleitoral | http://localhost:8080/cola-eleitoral |
| Fontes Oficiais | http://localhost:8080/fontes |
| Metodologia | http://localhost:8080/metodologia |
| Status e Auditoria | http://localhost:8080/status |
| Health Check API | http://localhost:8080/api/v1/health |

---

## 🤖 Configuração da API NVIDIA (AI Endpoints)

O projeto integra com a **NVIDIA AI Endpoints** para fornecer funcionalidades de Inteligência Artificial, incluindo análise de candidatos e explicações sobre cargos políticos.

### Obtenha sua API Key

1. Acesse [https://build.nvidia.com/](https://build.nvidia.com/)
2. Crie uma conta ou faça login
3. Gere uma API Key em **API Catalog**

### Configure no `.env`

```env
NVIDIA_API_KEY=sua_chave_api_aqui
NVIDIA_DEFAULT_MODEL=meta/llama-3.1-8b-instruct
NVIDIA_MAX_TOKENS=1024
NVIDIA_TEMPERATURE=0.7
```

### Reinicie os Contêineres

```bash
docker compose restart php
```

### Teste a Conexão

```bash
curl http://localhost:8080/api/v1/nvidia/status
```

### Funcionalidades Disponíveis

- **Análise de Candidatos**: Gera resumos factuais e neutros sobre candidatos
- **Explicação de Cargos**: Explica funções políticas de forma didática
- **Chat com IA**: Conversa genérica com modelos de linguagem NVIDIA
- **Embeddings**: Gera representações vetoriais para textos

---

## 📡 API REST

Todos os dados públicos estão disponíveis via API REST em formato JSON padronizado.

### Endpoints Principais

| Método | Endpoint | Descrição |
|---|---|---|
| `GET` | `/api/v1/health` | Status da aplicação, banco e última importação |
| `GET` | `/api/v1/elections` | Lista as eleições cadastradas |
| `GET` | `/api/v1/offices` | Lista os cargos em disputa (Presidente, Governador, etc.) |
| `GET` | `/api/v1/parties` | Lista todos os partidos registrados no TSE |
| `GET` | `/api/v1/candidates` | Busca paginada de candidatos com filtros |
| `GET` | `/api/v1/candidates/{id}` | Ficha detalhada do candidato com fontes e propostas |
| `GET` | `/api/v1/candidates/{id}/sources` | Fontes auditáveis associadas ao candidato |
| `GET` | `/api/v1/candidates/{id}/proposals`| Propostas temáticas registradas |
| `GET` | `/api/v1/candidates/{id}/records` | Histórico e registros públicos |
| `GET` | `/api/v1/imports/status` | Relatório das últimas importações |

**Filtros disponíveis para `/api/v1/candidates`:**

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `name` | string | Nome do candidato (busca parcial) |
| `ballot_number` | string | Número do candidato na urna |
| `office` | string | Cargo (ex: "Presidente", "Governador") |
| `party` | string | Sigla do partido (ex: "PT", "PSDB") |
| `state_code` | string | UF (ex: "SP", "RJ") |

**Exemplo de requisição:**
```bash
curl "http://localhost:8080/api/v1/candidates?party=PT&state_code=SP"
```

### 🤖 NVIDIA AI Endpoints

| Método | Endpoint | Descrição |
|---|---|---|
| `GET` | `/api/v1/nvidia/status` | Status da configuração da API NVIDIA |
| `GET` | `/api/v1/nvidia/models` | Lista modelos disponíveis na NVIDIA |
| `POST` | `/api/v1/nvidia/analyze-candidate` | Analisa candidato usando IA (resumo factual) |
| `POST` | `/api/v1/nvidia/explain-office` | Explica um cargo político de forma didática |
| `POST` | `/api/v1/nvidia/chat` | Chat genérico com modelos NVIDIA |
| `POST` | `/api/v1/nvidia/embeddings` | Gera embeddings para textos |

**Exemplo de análise de candidato:**
```bash
curl -X POST http://localhost:8080/api/v1/nvidia/analyze-candidate \
  -H "Content-Type: application/json" \
  -d '{
    "candidate": {
      "name": "João Silva",
      "ballot_number": "12345",
      "party_abbreviation": "PT",
      "office_name": "Deputado Federal",
      "state_code": "SP",
      "age": 45,
      "education": "Superior Completo"
    }
  }'
```

**Exemplo de explicação de cargo:**
```bash
curl -X POST http://localhost:8080/api/v1/nvidia/explain-office \
  -H "Content-Type: application/json" \
  -d '{"office_name": "Governador"}'
```

**Exemplo de chat genérico:**
```bash
curl -X POST http://localhost:8080/api/v1/nvidia/chat \
  -H "Content-Type: application/json" \
  -d '{
    "prompt": "O que é um deputado federal?",
    "max_tokens": 500
  }'
```

### Respostas da API

**Sucesso:**
```json
{
  "success": true,
  "data": { ... }
}
```

**Erro:**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "O campo 'prompt' é obrigatório.",
    "details": []
  }
}
```

---

## 🧪 Testes Automatizados

O projeto conta com suíte de testes unitários e de integração utilizando PHPUnit.

### Executar Todos os Testes

```bash
docker compose exec php ./vendor/bin/phpunit
```

### Executar Apenas Testes Unitários

```bash
docker compose exec php ./vendor/bin/phpunit --testsuite=Unit
```

### Executar Apenas Testes de Integração

```bash
docker compose exec php ./vendor/bin/phpunit --testsuite=Integration
```

### Gerar Relatório de Cobertura

```bash
docker compose exec php ./vendor/bin/phpunit --coverage-html=coverage
```

---

## 📁 Estrutura de Diretórios

```text
EleitorConsciente2026/
├── Dockerfile                  # Container PHP 8.2 + Apache + Composer
├── docker-compose.yml          # Definição dos serviços php e mysql
├── start.bat / stop.bat        # Scripts de conveniência para Windows
├── .env.example / .env         # Variáveis de ambiente
├── composer.json / phpunit.xml # Autoload PSR-4 e testes
├── database/
│   ├── migrations/             # Esquema relacional MySQL
│   ├── seeds/                  # Carga de eleições, cargos, partidos e fontes
│   └── fixtures/               # CSV de teste no padrão de colunas do TSE
├── docs/                       # Documentação técnica e metodológica
│   ├── architecture.md         # Arquitetura do sistema
│   ├── data-sources.md         # Fontes oficiais e integração
│   ├── data-dictionary.md      # Dicionário de dados
│   ├── methodology.md          # Princípios éticos e neutralidade
│   ├── privacy.md              # Conformidade com a LGPD
│   ├── security.md             # Práticas de segurança
│   ├── deployment.md           # Guia de implantação
│   └── operations.md           # Guia operacional
├── importer/                   # Módulo de importação e auditoria
│   ├── Contracts/              # Interfaces de adaptadores
│   ├── Adapters/               # Adaptador do TSE (leitura e streaming CSV)
│   ├── Normalizers/            # Normalização de campos do TSE
│   ├── Validators/             # Validação cadastral
│   ├── Jobs/                   # Job de importação em lotes e auditoria
│   └── cli.php                 # Runner CLI de banco e importações
├── public/                     # DocumentRoot Apache (exposição pública)
│   ├── index.php               # Front controller
│   ├── .htaccess               # Reescrita de URLs e headers de segurança
│   ├── manifest.webmanifest    # Manifesto PWA
│   └── assets/                 # CSS, JavaScript e imagens
├── routes/                     # Definições de rotas (web.php e api.php)
├── src/                        # Código fonte modular (PSR-4 App\)
│   ├── Config/                 # Leitor de ambiente (.env)
│   ├── Database/               # Conexão PDO e migrador
│   ├── Domain/                 # Controladores e repositórios de domínio
│   │   ├── Candidate/          # Candidatos
│   │   ├── Election/           # Eleições, cargos e partidos
│   │   ├── Health/             # Health check
│   │   ├── Import/             # Importação de dados
│   │   ├── Nvidia/             # Integração NVIDIA AI
│   │   └── Web/                # Páginas web
│   ├── Http/                   # Request, Response e Router
│   └── Support/                # Sanitização, Logger e NvidiaClient
├── tests/                      # Testes unitários e de integração
│   ├── Unit/                   # Testes unitários
│   └── Integration/            # Testes de integração com banco
└── views/                      # Templates de apresentação HTML5
    ├── layout/                 # Header e Footer
    ├── candidates/             # Páginas de candidatos
    └── errors/                 # Páginas de erro
```

---

## 🔧 Troubleshooting

### Docker não inicia os contêineres

**Problema:** Erro "port is already allocated"

**Solução:** Verifique se a porta 8080 ou 3306 já está em uso:
```powershell
netstat -ano | findstr :8080
netstat -ano | findstr :3306
```

Se estiver em uso, pare o processo ou altere as portas no `docker-compose.yml`.

### Erro de conexão com o banco

**Problema:** "Falha na conexão com o banco de dados"

**Solução:** Aguarde alguns segundos após iniciar os contêineres para que o MySQL esteja pronto. Verifique os logs:
```powershell
docker compose logs mysql
```

### Migrações falham

**Problema:** Erro ao executar migrações

**Solução:** Verifique se o MySQL está rodando e acessível:
```powershell
docker compose exec mysql mysql -u root -proot -e "SHOW DATABASES;"
```

### API NVIDIA não funciona

**Problema:** Erro "NVIDIA API key não configurada"

**Solução:** Verifique se a variável `NVIDIA_API_KEY` está definida no arquivo `.env` e reinicie o contêiner PHP:
```powershell
docker compose restart php
```

### Limpeza completa do ambiente

Para remover todos os contêineres, volumes e dados:
```powershell
docker compose down -v --rmi all
```

---

## 📄 Licença

Este projeto é software livre licenciado sob a [Licença MIT](LICENSE).

## 🤝 Contribuições

Consulte nosso [Guia de Contribuição](CONTRIBUTING.md) e [Código de Conduta](CODE_OF_CONDUCT.md).

## 🔒 Segurança

Para reportar vulnerabilidades, consulte nossa [Política de Segurança](SECURITY.md).

---

**Feito com ❤️ para a democracia brasileira.**
