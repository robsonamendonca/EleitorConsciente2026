# Relatório de Descoberta e Validação - Fase 0
# Projeto Eleitor Consciente 2026

## 1. Inspeção do Ambiente Local

- **Sistema Operacional**: Windows 11 / Windows Server compatível com PowerShell.
- **Docker**: Docker Engine 29.7.2 e Docker Compose v5.5.1 instalados e operacionais (Docker Desktop com backend WSL2).
- **PHP e Composer no Host**: Não instalados diretamente no PATH do host Windows; todo o ciclo de vida do backend (PHP 8.2, Composer, PDO e extensões) será executado e gerenciado dentro do contêiner Docker oficial.
- **Banco de Dados**: MySQL 8.0 em contêiner Docker com volume persistente (`mysql_data`).
- **Comunicação Web**: Apache HTTP Server na porta `8080`, mapeada para porta `80` do contêiner.

---

## 2. Análise do Repositório

- **Estado Inicial**: Repositório continha apenas `docker-compose.yml`, `start.bat`, `stop.bat`, `eleitor-consciente-2026-PRD.md` e um `README.md` embrionário.
- **Objetivo**: Estruturar a aplicação modular monolítica completa sem dependência de frameworks externos pesados, garantindo alta compatibilidade, portabilidade para hospedagem compartilhada e auditabilidade total.

---

## 3. Investigação das Fontes Oficiais de Dados Eleitorais

### 3.1 Portal de Dados Abertos do TSE
- **URL Base**: `https://dadosabertos.tse.jus.br/`
- **Conjunto Principal**: Candidatos (`consulta_cand_ANO_UF.csv`)
- **Layout de Dados (Colunas Chave)**:
  - `ANO_ELEICAO`: Ano da eleição (ex: 2026).
  - `SG_UF`: Sigla da Unidade Federativa (ex: `SP`, ou `BR` para presidente).
  - `CD_CARGO` e `DS_CARGO`: Código e descrição do cargo (Presidente, Governador, Senador, Deputado Federal, Deputado Estadual).
  - `SQ_CANDIDATO`: Identificador único do candidato na Justiça Eleitoral (`tse_id`), chave primária do TSE usada para correlacionar bens, receitas e processos.
  - `NR_CANDIDATO`: Número do candidato na urna (tratado como string para preservar zeros à esquerda).
  - `NM_CANDIDATO`: Nome civil completo do candidato.
  - `NM_URNA_CANDIDATO`: Nome que aparece na urna eletrônica.
  - `SG_PARTIDO`: Sigla do partido político.
  - `NM_PARTIDO`: Nome completo do partido.
  - `DS_SITUACAO_CANDIDATURA`: Situação cadastral (ex: `DEFERIDO`, `DEFERIDO COM RECURSO`, `AGUARDANDO JULGAMENTO`, `INDEFERIDO`).
  - `DT_ULTIMA_ATUALIZACAO`: Carimbo de data/hora oficial da extração.
- **DivulgaCandContas**:
  - Interface e API pública para consulta visual, fotos oficiais (`/candidatura/buscar/foto/...`) e propostas de governo em PDF.

### 3.2 Limitações e Riscos Identificados
1. **Disponibilidade Antecipada**: As candidaturas para as Eleições Gerais de 2026 só são homologadas e registradas pelo TSE a partir de julho/agosto de 2026 (Convenções Partidárias). Para permitir o desenvolvimento, testes e simulações completas antes do período eleitoral, o sistema deve suportar fixtures de teste fiéis ao layout oficial do TSE.
2. **Encoding e Delimitador**: Arquivos CSV do TSE tipicamente utilizam codificação `ISO-8859-1` ou `UTF-8` e delimitador ponto e vírgula (`;`). O importador deve detectar e normalizar esses padrões automaticamente.
3. **Volume de Dados**: O arquivo estadual de São Paulo pode conter milhares de registros. O leitor não deve carregar todo o arquivo na memória (`memory_limit`), mas sim ler linha a linha via streams (geradores PHP `iterable`).
4. **LGPD e Dados Pessoais**: O arquivo bruto do TSE pode conter dados como CPF ou dados de certidão. O normalizador do sistema **deve descartar imediatamente** qualquer dado pessoal sensível ou desnecessário, persistindo apenas informações públicas e estritamente eleitorais.

---

## 4. Proposta do Esquema de Dados Final

O modelo relacional adotado segue estritamente as diretrizes da Seção 9 do PRD:
1. `elections`: Registro das eleições por ano e tipo.
2. `offices`: Cargos em disputa (`PRESIDENTE`, `GOVERNADOR`, `SENADOR`, `DEPUTADO_FEDERAL`, `DEPUTADO_ESTADUAL`).
3. `parties`: Partidos políticos registrados.
4. `candidates`: Registro unificado com `tse_id`, número de urna, nomes, foto, partido e situação.
5. `sources`: Fontes públicas e seus níveis de autoridade.
6. `candidate_sources`: Rastreabilidade de cada dado do candidato com URL e hash.
7. `candidate_proposals`: Propostas temáticas por categoria.
8. `candidate_records`: Histórico público de mandatos e votações anteriores.
9. `data_imports`: Auditoria de cada execução de importação com hash, contadores de inseridos, atualizados e rejeitados.
10. `import_errors`: Log detalhado de falhas de validação de linhas para auditoria.
11. `voter_checklists` e `voter_checklist_items`: Estrutura de dados preparada para exportação/sincronização opcional da cola eleitoral.

---

## 5. Próximos Passos
Prosseguir para a Fase 1: Criação do ambiente Docker completo (`Dockerfile`, `docker-compose.yml`), migrações do banco de dados, sementes (seeds), fixtures, estrutura modular do backend e testes de saúde (`health check`).
