# PRD e Guia de Implementação
# Eleitor Consciente 2026

## 1. Identificação do projeto

Nome provisório: Eleitor Consciente 2026

Tipo: Projeto open source de informação eleitoral

Público-alvo: Eleitores brasileiros, inicialmente com foco no estado de São Paulo

Objetivo: Criar uma plataforma pública, transparente e auditável que reúna dados eleitorais oficiais, permita a consulta de candidatos e ajude o eleitor a organizar sua própria cola eleitoral e seus critérios pessoais de análise.

Este documento deve ser usado como especificação principal por agentes de código, incluindo Claude Code, OpenCode, Codex, agentes baseados em modelos da Anthropic, Google ou outras ferramentas compatíveis com desenvolvimento assistido por IA.

O agente deve ler este documento integralmente antes de iniciar a implementação.

---

## 2. Objetivo do produto

Construir uma aplicação web mobile-first, acessível e open source, capaz de:

1. Importar e atualizar dados públicos de candidatos a partir de fontes oficiais.
2. Consultar candidatos por estado, cargo, partido, nome e número de urna.
3. Exibir fichas individuais com informações, fontes e datas de atualização.
4. Apresentar propostas, histórico público e dados eleitorais disponíveis.
5. Permitir que o eleitor registre observações e critérios pessoais.
6. Permitir a criação de uma cola eleitoral personalizada.
7. Disponibilizar os dados por uma API REST.
8. Registrar versões, erros e resultados das importações.
9. Evitar recomendações automáticas de voto, rankings políticos ou favorecimento de candidatos.
10. Manter o projeto verificável, documentado, testável e fácil de instalar.

A plataforma deve informar, não persuadir. A decisão eleitoral pertence ao usuário.

---

## 3. Escopo inicial

### 3.1 Escopo do MVP

O MVP deve atender inicialmente ao estado de São Paulo e às eleições gerais de 2026.

Funcionalidades obrigatórias:

- Cadastro/importação de candidatos.
- Identificador oficial do TSE, quando disponível.
- Estado e cargo.
- Nome completo.
- Nome de urna.
- Número de urna.
- Partido e federação, quando disponíveis.
- Situação da candidatura.
- Foto oficial, quando disponível.
- Busca por nome.
- Busca por número.
- Filtros por cargo, partido e situação.
- Página detalhada do candidato.
- Exibição da fonte e data de atualização.
- Controle de importações.
- API REST.
- Cola eleitoral local.
- Documentação de instalação.
- Testes automatizados básicos.
- Política de correção de dados.
- Aviso claro de que a plataforma não recomenda candidatos.

### 3.2 Fora do escopo inicial

Não implementar na primeira versão:

- Sistema de propaganda eleitoral.
- Venda de destaque para candidatos.
- Ranking geral de candidatos.
- Nota automática de honestidade, competência ou qualidade.
- Inferência de intenção de voto.
- Coleta obrigatória de nome, CPF, telefone ou endereço.
- Armazenamento obrigatório da escolha eleitoral no servidor.
- Comentários públicos sem moderação.
- Treinamento de modelo de IA próprio.
- Rastreamento invasivo de usuários.
- Integração com dados não verificáveis apresentados como oficiais.

---

## 4. Princípios do projeto

### 4.1 Neutralidade informacional

A aplicação deve apresentar dados verificáveis sem recomendar ou desaconselhar candidatos.

### 4.2 Transparência

Cada dado relevante deve apresentar:

- Fonte.
- URL, quando disponível.
- Data de coleta.
- Data da última atualização.
- Tipo da fonte.
- Limitações conhecidas.

### 4.3 Separação de camadas

Distinguir claramente:

- Dado oficial.
- Declaração do candidato.
- Informação de fonte externa.
- Análise documental.
- Comentário pessoal do eleitor.
- Conteúdo gerado por IA.

### 4.4 Privacidade por padrão

A cola eleitoral deve funcionar localmente sempre que possível.

Não coletar dados pessoais desnecessários.

### 4.5 Auditabilidade

O processo de importação deve registrar:

- Arquivo de origem.
- URL.
- Hash do arquivo.
- Horário da coleta.
- Quantidade de registros processados.
- Quantidade inserida.
- Quantidade atualizada.
- Quantidade rejeitada.
- Mensagens de erro.
- Status final.

### 4.6 Acessibilidade

A interface deve funcionar em celulares, tablets e computadores.

Utilizar HTML semântico, contraste adequado, navegação por teclado, labels acessíveis e mensagens claras.

---

## 5. Fontes de dados

A fonte principal deve ser o Tribunal Superior Eleitoral, especialmente os conjuntos de dados públicos e o portal DivulgaCandContas.

Fontes possíveis:

1. Portal de Dados Abertos do TSE.
2. DivulgaCandContas.
3. Tribunal Regional Eleitoral de São Paulo.
4. Câmara dos Deputados.
5. Senado Federal.
6. Assembleia Legislativa do Estado de São Paulo.
7. Outras fontes institucionais públicas, quando necessário.

### Regras para fontes

- Não inventar URLs.
- Não assumir que um endpoint existe sem validação.
- Documentar cada fonte usada.
- Registrar a data da última verificação.
- Não tratar uma fonte secundária como fonte oficial.
- Validar o formato real dos arquivos antes de criar o importador definitivo.
- Criar adaptadores separados para cada fonte.
- Permitir que uma fonte fique temporariamente indisponível sem interromper todo o sistema.

O agente deve validar a disponibilidade atual dos portais e arquivos antes de implementar o importador. Se a internet não estiver disponível no ambiente de desenvolvimento, criar interfaces, mocks e fixtures realistas, sem inventar dados oficiais.

---

## 6. Stack tecnológica recomendada

Priorizar simplicidade, baixo custo e compatibilidade com hospedagem compartilhada.

### Backend

- PHP 8.2 ou versão estável compatível com o ambiente escolhido.
- PDO.
- MySQL 8 ou MariaDB compatível.
- API REST.
- Composer, quando disponível.
- PSR-4 para autoload.
- Variáveis de ambiente para configuração.

### Frontend

- HTML5.
- CSS3.
- JavaScript vanilla.
- Layout responsivo mobile-first.
- PWA opcional no MVP, mas preparado para expansão.
- Sem dependência obrigatória de frameworks pesados.

### Testes

- PHPUnit para backend.
- Testes de integração para importadores.
- Testes de API.
- Testes de validação de dados.
- Testes manuais documentados para frontend.

### Qualidade

- PHP_CodeSniffer ou ferramenta equivalente.
- PHPStan, quando compatível.
- ESLint opcional para JavaScript.
- Verificação de segurança em cada etapa.
- GitHub Actions para CI.

### Infraestrutura

- Desenvolvimento local com PHP, MySQL e servidor embutido ou Docker opcional.
- Produção compatível com hospedagem compartilhada.
- Cron, quando disponível.
- Execução manual protegida como fallback, sem permitir que qualquer usuário execute importações administrativas.

---

## 7. Arquitetura proposta

Arquitetura modular monolítica simples.

```text
Fontes oficiais
      |
      v
Adaptadores de importação
      |
      v
Validação e normalização
      |
      v
Tabelas temporárias
      |
      v
Comparação e persistência
      |
      v
Banco MySQL
      |
      +------------------+
      |                  |
      v                  v
API REST           Interface Web/PWA
      |                  |
      +--------+---------+
               |
               v
           Eleitor
```

### Módulos

- Authentication, se necessário para área administrativa.
- Candidates.
- Elections.
- Offices.
- Parties.
- Sources.
- Imports.
- Proposals.
- Public records.
- Voter checklist.
- Audit logs.
- API.
- Administration.
- Health checks.

Evitar acoplamento entre a importação e a interface pública.

---

## 8. Estrutura de diretórios

```text
eleitor-consciente-2026/
├── README.md
├── LICENSE
├── CONTRIBUTING.md
├── CODE_OF_CONDUCT.md
├── SECURITY.md
├── CHANGELOG.md
├── .env.example
├── composer.json
├── phpunit.xml
├── database/
│   ├── migrations/
│   ├── seeds/
│   └── fixtures/
├── docs/
│   ├── architecture.md
│   ├── data-sources.md
│   ├── data-dictionary.md
│   ├── methodology.md
│   ├── privacy.md
│   ├── security.md
│   ├── deployment.md
│   └── operations.md
├── public/
│   ├── index.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── manifest.webmanifest
├── src/
│   ├── Config/
│   ├── Database/
│   ├── Http/
│   ├── Domain/
│   │   ├── Candidate/
│   │   ├── Election/
│   │   ├── Import/
│   │   ├── Source/
│   │   └── Checklist/
│   ├── Repositories/
│   ├── Services/
│   ├── Validators/
│   └── Support/
├── routes/
│   ├── web.php
│   └── api.php
├── importer/
│   ├── Contracts/
│   ├── Adapters/
│   ├── Validators/
│   ├── Normalizers/
│   └── Jobs/
├── tests/
│   ├── Unit/
│   ├── Integration/
│   └── Fixtures/
├── storage/
│   ├── logs/
│   ├── cache/
│   └── imports/
└── .github/
    └── workflows/
        └── ci.yml
```

O agente pode adaptar a estrutura caso escolha um framework, mas deve preservar a separação de responsabilidades.

---

## 9. Modelo de dados

### 9.1 elections

Campos sugeridos:

- id
- year
- name
- election_type
- status
- created_at
- updated_at

Restrições:

- O ano deve ser inteiro válido.
- O nome deve ser obrigatório.
- Deve existir índice para year.

### 9.2 offices

Campos sugeridos:

- id
- code
- name
- level
- created_at
- updated_at

Exemplos:

- PRESIDENTE
- GOVERNADOR
- SENADOR
- DEPUTADO_FEDERAL
- DEPUTADO_ESTADUAL

Não assumir códigos internos do TSE sem consultar a documentação ou os arquivos reais.

### 9.3 candidates

Campos sugeridos:

- id
- election_id
- tse_id
- office_id
- state_code
- municipality_code, quando aplicável
- ballot_number
- ballot_name
- full_name
- party_acronym
- federation_name
- registration_status
- photo_url
- source_last_updated_at
- created_at
- updated_at

Regras:

- `tse_id` deve ser usado quando disponível.
- Criar uma chave única adequada para evitar duplicidade.
- Não usar apenas o nome como identificador.
- Números devem ser tratados como texto para preservar zeros à esquerda, quando existirem.
- Valores ausentes devem permanecer nulos, não receber valores inventados.

### 9.4 parties

Campos sugeridos:

- id
- acronym
- name
- number, quando disponível
- created_at
- updated_at

### 9.5 sources

Campos sugeridos:

- id
- source_type
- name
- url
- authority_level
- active
- created_at
- updated_at

### 9.6 candidate_sources

Campos sugeridos:

- id
- candidate_id
- source_id
- source_reference
- reference_date
- retrieved_at
- content_hash
- notes

### 9.7 candidate_proposals

Campos sugeridos:

- id
- candidate_id
- title
- description
- category
- source_id
- source_url
- source_date
- verification_status
- created_at
- updated_at

Categorias possíveis:

- Saúde
- Educação
- Segurança
- Economia
- Trabalho
- Infraestrutura
- Meio ambiente
- Administração pública
- Direitos sociais
- Outros

Não classificar uma proposta como verdadeira ou falsa automaticamente sem metodologia documentada e revisão adequada.

### 9.8 candidate_records

Para registrar informações públicas adicionais:

- id
- candidate_id
- record_type
- title
- description
- source_id
- source_url
- reference_date
- verification_status
- created_at
- updated_at

Tipos possíveis:

- Mandato
- Votação
- Projeto
- Comissão
- Declaração pública
- Prestação de contas
- Outro

### 9.9 data_imports

Campos:

- id
- dataset_name
- source_id
- source_url
- file_name
- file_hash
- started_at
- finished_at
- records_processed
- records_inserted
- records_updated
- records_rejected
- status
- error_message
- created_at

Status possíveis:

- pending
- running
- completed
- partial
- failed

### 9.10 import_errors

Campos:

- id
- data_import_id
- row_reference
- field_name
- error_code
- error_message
- raw_value
- created_at

Não armazenar dados pessoais desnecessários nos logs.

### 9.11 voter_checklists

Preferir funcionamento local no navegador.

Se houver persistência no servidor:

- id
- anonymous_token_hash
- title
- election_id
- state_code
- created_at
- updated_at

Não armazenar a escolha eleitoral de forma identificável sem necessidade clara, consentimento e proteção adequada.

### 9.12 voter_checklist_items

Campos:

- id
- checklist_id
- office_id
- candidate_id
- custom_note
- position_order
- created_at
- updated_at

Validar se o candidato pertence à eleição e ao cargo selecionado.

---

## 10. API REST

A API deve retornar JSON consistente.

### Padrão de resposta de sucesso

```json
{
  "success": true,
  "data": {},
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 0
  }
}
```

### Padrão de erro

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Dados inválidos.",
    "details": []
  }
}
```

### Endpoints públicos

```text
GET /api/v1/elections
GET /api/v1/elections/{id}
GET /api/v1/offices
GET /api/v1/parties
GET /api/v1/candidates
GET /api/v1/candidates/{id}
GET /api/v1/candidates/{id}/sources
GET /api/v1/candidates/{id}/proposals
GET /api/v1/candidates/{id}/records
GET /api/v1/imports/status
GET /api/v1/health
```

### Parâmetros de busca de candidatos

- election_id
- year
- state_code
- office
- party
- status
- ballot_number
- name
- page
- per_page
- sort

Regras:

- Limitar `per_page`.
- Sanitizar e validar todos os parâmetros.
- Utilizar consultas parametrizadas.
- Evitar ordenação por campos arbitrários.
- Retornar paginação.
- Permitir ordenação apenas por campos autorizados.

### Endpoints administrativos

Devem exigir autenticação e autorização.

```text
POST /api/v1/admin/imports
GET /api/v1/admin/imports
GET /api/v1/admin/imports/{id}
POST /api/v1/admin/imports/{id}/retry
GET /api/v1/admin/import-errors
```

Não permitir que usuários anônimos iniciem importações.

---

## 11. Importador de dados

### Fluxo obrigatório

1. Identificar a fonte.
2. Verificar disponibilidade.
3. Baixar o arquivo ou consultar o recurso.
4. Calcular hash.
5. Verificar se o conteúdo já foi importado.
6. Registrar a execução.
7. Validar cabeçalhos e estrutura.
8. Ler os registros em lotes.
9. Normalizar campos.
10. Validar os dados.
11. Persistir em tabelas temporárias.
12. Comparar com dados existentes.
13. Aplicar alterações em transação quando possível.
14. Registrar erros individuais.
15. Finalizar o processo com status adequado.
16. Disponibilizar relatório da importação.

### Regras do importador

- Não interromper toda a importação por causa de um registro inválido.
- Não sobrescrever dados válidos com valores vazios sem regra explícita.
- Não apagar registros automaticamente por ausência em um arquivo sem confirmação da estratégia.
- Manter histórico suficiente para auditoria.
- Suportar reexecução idempotente.
- Usar processamento em lotes.
- Evitar carregar arquivos gigantes inteiros na memória.
- Validar encoding, delimitador, cabeçalhos e tipos.
- Registrar a versão do parser utilizado.

### Contrato de adaptador

Criar uma interface semelhante a:

```php
interface DataSourceAdapterInterface
{
    public function getSourceName(): string;

    public function discover(): array;

    public function download(array $resource): string;

    public function parse(string $filePath): iterable;

    public function normalize(array $row): array;

    public function validate(array $row): array;
}
```

A assinatura pode ser adaptada, desde que o contrato seja documentado e testado.

---

## 12. Interface do usuário

### Páginas obrigatórias

1. Página inicial.
2. Busca de candidatos.
3. Lista de candidatos.
4. Detalhe do candidato.
5. Minhas fontes.
6. Minha cola eleitoral.
7. Metodologia.
8. Sobre o projeto.
9. Política de privacidade.
10. Página de status dos dados.
11. Página de erro e estado vazio.

### Página inicial

Deve apresentar:

- Objetivo do projeto.
- Campo de busca.
- Seleção de estado.
- Seleção de cargo.
- Link para metodologia.
- Data da última atualização.
- Aviso de neutralidade.
- Link para o repositório GitHub.

### Lista de candidatos

Cada cartão ou linha deve exibir:

- Foto, quando disponível.
- Nome de urna.
- Nome completo.
- Número.
- Partido.
- Cargo.
- Situação.
- Última atualização.
- Link para detalhes.

Não destacar candidatos por critérios comerciais.

### Página de detalhes

Seções:

- Identificação.
- Número de urna.
- Partido e federação.
- Situação.
- Propostas.
- Histórico público.
- Dados financeiros, quando disponíveis.
- Fontes.
- Data da atualização.
- Limitações e observações.

### Cola eleitoral

Recursos:

- Selecionar candidato por cargo.
- Alterar candidato.
- Remover candidato.
- Reordenar itens.
- Adicionar anotação local.
- Imprimir.
- Exportar para PDF, se viável.
- Salvar no navegador.
- Limpar dados locais.
- Exibir aviso de conferência do número no dia da votação.

A cola deve ser apresentada como organização pessoal do eleitor.

---

## 13. Modelo de análise pessoal

A aplicação pode permitir que o eleitor registre critérios próprios, sem gerar uma avaliação oficial da plataforma.

Critérios sugeridos:

- Clareza das propostas.
- Compatibilidade com prioridades pessoais.
- Histórico documentado.
- Transparência das informações.
- Experiência profissional e política.
- Coerência entre declarações e ações documentadas.

O sistema deve:

- Mostrar as fontes usadas.
- Permitir notas pessoais.
- Indicar informações ausentes.
- Evitar atribuir automaticamente conceitos como honesto, desonesto, competente ou incompetente.
- Não produzir um vencedor geral.
- Não inferir intenção de voto.
- Não usar linguagem persuasiva.

Se for implementado um sistema de pontuação, ele deve ser explicitamente definido como uma avaliação pessoal do usuário e não como uma classificação da plataforma.

---

## 14. Segurança

Implementar, no mínimo:

- PDO com prepared statements.
- Validação de entrada no servidor.
- Escape de saída HTML.
- Proteção contra XSS.
- Proteção contra CSRF em operações com sessão.
- Rate limiting em endpoints sensíveis.
- Autorização para rotas administrativas.
- Segredos apenas em variáveis de ambiente.
- Logs sem senhas, tokens ou dados sensíveis.
- Controle de upload e validação de arquivos.
- Limite de tamanho para requisições.
- Headers de segurança.
- Política de conteúdo quando viável.
- Tratamento seguro de erros.
- Backup documentado.
- Rotação de logs.
- Verificação de dependências.

Nunca exibir stack trace em produção.

---

## 15. Privacidade e LGPD

Criar `docs/privacy.md` com linguagem simples.

Princípios:

- Minimização de dados.
- Finalidade clara.
- Transparência.
- Segurança.
- Retenção limitada.
- Exclusão de dados quando aplicável.
- Preferência por armazenamento local.
- Não coletar opinião política identificável sem necessidade.
- Não vender dados.
- Não compartilhar dados pessoais com terceiros sem base legal adequada.
- Não armazenar a cola no servidor por padrão.

A implementação deve ser revisada para evitar a coleta involuntária de dados sensíveis.

---

## 16. GitHub Actions

Criar pipeline com:

1. Checkout.
2. Instalação das dependências.
3. Validação de sintaxe PHP.
4. Execução de testes.
5. Análise estática, se configurada.
6. Verificação de padrões de código.
7. Verificação de arquivos sensíveis.
8. Geração de relatório.
9. Resultado visível no pull request.

Exemplo de etapas conceituais:

```text
push / pull_request
        |
        v
Install dependencies
        |
        v
Lint
        |
        v
Static analysis
        |
        v
Unit tests
        |
        v
Integration tests
        |
        v
Security checks
```

Não incluir `.env`, senhas, tokens ou dados reais de usuários no repositório.

---

## 17. Health checks

Criar:

```text
GET /api/v1/health
```

A resposta deve indicar:

- Status da aplicação.
- Status da conexão com banco, sem expor credenciais.
- Timestamp.
- Versão da aplicação.
- Última importação concluída.
- Status da última importação.

Exemplo:

```json
{
  "success": true,
  "data": {
    "application": "ok",
    "database": "ok",
    "last_import": {
      "status": "completed",
      "finished_at": "2026-09-21T10:00:00-03:00"
    }
  }
}
```

Não expor detalhes internos em ambientes públicos além do necessário.

---

## 18. Critérios de aceite do MVP

O MVP somente deve ser considerado concluído quando:

### Dados

- [ ] A fonte de dados foi documentada.
- [ ] O formato real dos arquivos foi validado.
- [ ] O importador processa registros válidos.
- [ ] Registros inválidos são registrados.
- [ ] A importação é idempotente.
- [ ] O sistema evita duplicidades.
- [ ] A data da atualização aparece na interface.
- [ ] Cada candidato possui fonte associada.

### Backend

- [ ] API REST funcional.
- [ ] Validação de parâmetros.
- [ ] Paginação.
- [ ] Consultas parametrizadas.
- [ ] Tratamento padronizado de erros.
- [ ] Health check funcional.
- [ ] Rotas administrativas protegidas.

### Frontend

- [ ] Busca por nome.
- [ ] Busca por número.
- [ ] Filtro por cargo.
- [ ] Filtro por partido.
- [ ] Página detalhada.
- [ ] Fontes visíveis.
- [ ] Layout responsivo.
- [ ] Cola eleitoral funcional.
- [ ] Armazenamento local funcionando.
- [ ] Estados de carregamento, vazio e erro.

### Qualidade

- [ ] Testes automatizados básicos.
- [ ] CI executando no GitHub.
- [ ] README atualizado.
- [ ] Guia de instalação.
- [ ] Guia de configuração.
- [ ] Política de privacidade.
- [ ] Política de segurança.
- [ ] Licença open source definida.
- [ ] Nenhum segredo versionado.

---

## 19. Estratégia de desenvolvimento por fases

### Fase 0: Descoberta e validação

Entregas:

- Confirmar fontes oficiais.
- Validar arquivos reais.
- Documentar limitações.
- Definir esquema de dados final.
- Criar fixtures de teste.
- Confirmar ambiente local.

Não implementar com base em nomes de campos presumidos.

### Fase 1: Fundação

Entregas:

- Estrutura do projeto.
- Configuração.
- Banco de dados.
- Migrações.
- Autoload.
- Logging.
- Health check.
- CI inicial.

### Fase 2: Importação

Entregas:

- Contratos de adaptadores.
- Adaptador da fonte principal.
- Validação.
- Normalização.
- Importação em lotes.
- Controle de hashes.
- Relatório de execução.
- Testes com fixtures.

### Fase 3: API

Entregas:

- Endpoints de eleições.
- Endpoints de cargos.
- Endpoints de partidos.
- Endpoints de candidatos.
- Paginação.
- Filtros.
- Respostas padronizadas.
- Testes de API.

### Fase 4: Frontend

Entregas:

- Página inicial.
- Busca.
- Lista.
- Detalhe.
- Fontes.
- Metodologia.
- Responsividade.
- Acessibilidade básica.

### Fase 5: Cola eleitoral

Entregas:

- Seleção por cargo.
- Armazenamento local.
- Anotações.
- Reordenação.
- Impressão.
- Exportação, se viável.
- Limpeza dos dados locais.

### Fase 6: Segurança e publicação

Entregas:

- Revisão de segurança.
- Revisão de privacidade.
- Testes finais.
- Documentação de deploy.
- Configuração de domínio.
- Monitoramento.
- Publicação do repositório.
- Release inicial.

---

## 20. Regras para agentes de código

O agente deve seguir estas regras durante todo o desenvolvimento:

1. Ler o documento antes de codificar.
2. Não implementar funcionalidades fora do escopo sem registrar a proposta.
3. Não inventar dados eleitorais.
4. Não inventar URLs, endpoints ou campos oficiais.
5. Verificar o formato real das fontes.
6. Fazer mudanças pequenas e verificáveis.
7. Executar testes após cada alteração relevante.
8. Não apagar código funcional sem justificativa.
9. Não modificar configurações de produção sem confirmação.
10. Não inserir segredos no código.
11. Documentar decisões arquiteturais.
12. Informar arquivos alterados.
13. Informar comandos executados.
14. Informar testes aprovados e falhos.
15. Apontar riscos e limitações.
16. Preferir soluções simples.
17. Manter compatibilidade com PHP e MySQL definidos.
18. Evitar dependências desnecessárias.
19. Não usar dados reais de eleitores em testes.
20. Não gerar recomendações políticas ou ranking de candidatos.

### Formato obrigatório de cada etapa

Ao concluir uma etapa, o agente deve responder:

```text
ETAPA:
Objetivo da etapa.

IMPLEMENTADO:
- Item 1
- Item 2

ARQUIVOS ALTERADOS:
- caminho/arquivo.ext

TESTES EXECUTADOS:
- comando
- resultado

PENDÊNCIAS:
- Item pendente

RISCOS OU OBSERVAÇÕES:
- Observação relevante

PRÓXIMA ETAPA:
- Próxima ação recomendada
```

---

## 21. Definition of Done

Uma tarefa só está concluída quando:

- O código foi implementado.
- O código foi revisado.
- Os testes relevantes foram executados.
- A documentação foi atualizada.
- Não existem erros conhecidos não documentados.
- A implementação respeita a arquitetura.
- A implementação respeita segurança e privacidade.
- A implementação não inventa dados.
- A alteração pode ser reproduzida por outro desenvolvedor.

---

## 22. Primeira instrução para o agente

Comece pela Fase 0.

Não crie todo o sistema de uma vez.

Execute as seguintes ações:

1. Inspecione o ambiente atual.
2. Verifique se o repositório está vazio ou já possui código.
3. Identifique versões disponíveis de PHP, Composer e MySQL.
4. Verifique acesso à internet e disponibilidade das fontes oficiais.
5. Pesquise e documente o formato real dos dados eleitorais de 2026.
6. Não faça download de dados pessoais desnecessários.
7. Crie um relatório de descoberta em `docs/discovery-report.md`.
8. Apresente os riscos encontrados.
9. Proponha o esquema final de dados.
10. Aguarde validação humana antes de iniciar alterações estruturais, caso o ambiente ou as fontes estejam ambíguos.

Se o agente tiver permissão para executar o projeto inteiro sem aprovação intermediária, deve continuar por fases, criando commits lógicos e mantendo o relatório de progresso atualizado.

---

## 23. Licença

Escolher uma licença open source após avaliar:

- Permissão para uso comercial.
- Permissão para modificações.
- Obrigação de atribuição.
- Compatibilidade com dados de terceiros.
- Restrições das fontes oficiais.
- Necessidade de preservar avisos de copyright.

A licença do código não concede automaticamente direitos sobre bases de dados, imagens, documentos ou conteúdos de terceiros.

---

## 24. Resultado esperado

Ao final, o projeto deverá entregar:

- Repositório GitHub organizado.
- Aplicação web funcional.
- API documentada.
- Importador de dados públicos.
- Banco de dados versionado.
- Interface responsiva.
- Cola eleitoral personalizada.
- Fontes identificadas.
- Atualização controlada.
- Logs de importação.
- Testes automatizados.
- Pipeline CI.
- Documentação de instalação.
- Documentação de operação.
- Política de privacidade.
- Política de segurança.
- Metodologia transparente.
- Código pronto para contribuições open source.

O sistema deve ser uma ferramenta de consulta e organização pessoal. Ele não deve substituir a leitura das fontes oficiais nem determinar em quem o eleitor deve votar.
