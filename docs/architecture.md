# Arquitetura do Sistema - Eleitor Consciente 2026

## 1. Visão Geral

O **Eleitor Consciente 2026** é projetado seguindo uma arquitetura monolítica modular simples, priorizando baixo acoplamento, alta auditabilidade, facilidade de implantação em Docker e compatibilidade com hospedagens compartilhadas.

```text
Fontes Oficiais (TSE / DivulgaCandContas)
      |
      v
Adaptadores de Importação (importer/Adapters)
      |
      v
Validação e Normalização (importer/Validators & Normalizers)
      |
      v
Banco Relacional MySQL 8.0 (Tabelas e Auditoria)
      |
      +-------------------------+
      |                         |
      v                         v
API REST (/api/v1/...)    Interface Web / PWA (Vanilla JS/CSS)
      |                         |
      +------------+------------+
                   |
                   v
              Cidadão / Eleitor
```

---

## 2. Camadas do Sistema

### 2.1 Módulo de Importação (`importer/`)
- **Adaptadores**: Implementam `DataSourceAdapterInterface`, encapsulando leitura e streaming linha a linha de CSV/JSON oficiais.
- **Normalizadores**: Padronizam colunas e eliminam tokens de valores vazios do TSE (`#NULO#`, `#NE#`, `-1`).
- **Validadores**: Verificam tipos, integridade de número de urna e presença de identificador oficial (`tse_id`).
- **Auditoria de Execução**: Toda importação registra hash SHA-256 do arquivo, horários de início e término, e contadores de registros inseridos, atualizados e rejeitados em `data_imports` e `import_errors`.

### 2.2 Camada de Domínio e Dados (`src/Domain/` e `src/Database/`)
- **Repositórios**: Consultas parametrizadas via PDO para mitigar injeção de SQL.
- **Controles de Acesso**: Rotas de consulta pública desprovidas de autenticação; rotas administrativas de ingestão protegidas por chave de API.

### 2.3 Camada HTTP e Roteamento (`src/Http/` e `routes/`)
- **Router**: Roteador com suporte a verbos HTTP, parâmetros de rota (`/api/v1/candidates/{id}`) e respostas em JSON e HTML.
- **Headers de Segurança**: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection: 1; mode=block`.

### 2.4 Camada de Apresentação e Cola Eleitoral (`views/`, `public/assets/`)
- **Interface Mobile-First**: HTML5 semântico e CSS3 puro com suporte a WCAG AA.
- **Cola Eleitoral Local**: Armazenada estritamente no `localStorage` do navegador do usuário, garantindo privacidade absoluta (LGPD) e sem envio de votos ao servidor.
- **Modo Impressão**: Estilo `@media print` dedicado para gerar folha A4 limpa e legível para o dia da eleição.

