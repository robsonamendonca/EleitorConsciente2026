# Fontes de Dados - Eleitor Consciente 2026

## Fonte Principal

### Portal de Dados Abertos do TSE

- **URL:** https://dadosabertos.tse.jus.br/
- **Tipo:** CSV (delimitador `;`, encoding ISO-8859-1)
- **Nível:** OFICIAL
- **Status:** ✅ Ativo (requer acesso via navegador)

---

## Arquivos de Candidatos

O TSE fornece dados separados por escopo:

### Arquivo 1: Estadual (São Paulo)

- **Conjunto:** `candidatos-2026`
- **Arquivo:** `consulta_cand_2026_SP.csv`
- **Localização:** `database/fixtures/tse_candidatos_sp_2026.csv`
- **Cargos incluídos:**
  - Governador (CD_CARGO=3)
  - Vice-Governador (CD_CARGO=5)
  - Senador (CD_CARGO=6)
  - 1º Suplente (CD_CARGO=7)
  - 2º Suplente (CD_CARGO=8)
  - Deputado Federal (CD_CARGO=9)
  - Deputado Estadual (CD_CARGO=10)

### Arquivo 2: Nacional (Presidente e Vice)

- **Conjunto:** `candidatos-2026`
- **Arquivo:** `consulta_cand_2026_BR.csv` (ou `consulta_cand_2026.csv`)
- **Localização:** `database/fixtures/tse_candidatos_br_2026.csv`
- **Cargos incluídos:**
  - Presidente (CD_CARGO=1)
  - Vice-Presidente (CD_CARGO=2)
- **Nota:** SG_UF = 'BR' (nacional)

#### Colunas Principais

| Coluna | Descrição | Obrigatória |
|--------|-----------|-------------|
| `ANO_ELEICAO` | Ano da eleição | ✅ |
| `SG_UF` | Sigla da UF (ex: SP) | ✅ |
| `CD_CARGO` | Código do cargo (1=Presidente, 3=Governador, etc.) | ✅ |
| `DS_CARGO` | Descrição do cargo | ✅ |
| `SQ_CANDIDATO` | Sequencial do candidato (TSE ID) | ✅ |
| `NR_CANDIDATO` | Número do candidato na urna | ✅ |
| `NM_CANDIDATO` | Nome completo do candidato | ✅ |
| `NM_URNA_CANDIDATO` | Nome de urna | ✅ |
| `SG_PARTIDO` | Sigla do partido | ✅ |
| `DS_SITUACAO_CANDIDATURA` | Situação da candidatura | ✅ |
| `DS_DETALHE_SITUACAO_CAND` | Detalhe da situação | ✅ |
| `DT_ELEICAO` | Data da eleição | ✅ |

#### Códigos de Cargos

| Código | Cargo | Eleição 2026 |
|--------|-------|--------------|
| 1 | Presidente | ✅ Nacional |
| 2 | Vice-Presidente | ✅ Nacional |
| 3 | Governador | ✅ Estadual |
| 5 | Vice-Governador | ✅ Estadual |
| 6 | Senador | ✅ Estadual |
| 7 | 1º Suplente | ✅ Estadual |
| 8 | 2º Suplente | ✅ Estadual |
| 9 | Deputado Federal | ✅ Estadual |
| 10 | Deputado Estadual | ✅ Estadual |

#### Filtros de Elegibilidade

O importador filtra automaticamente candidatos com:
- `DS_SITUACAO_CANDIDATURA = 'DEFERIDO'`
- `DS_DETALHE_SITUACAO_CAND = 'DEFERIDO'`
- `ANO_ELEICAO = 2026`
- `SG_UF = 'SP'` (ou UF desejada)

---

## Fontes Complementares

### DivulgaCandContas

- **URL:** https://divulgacandcontas.tse.jus.br/
- **Tipo:** API REST (JSON)
- **Nível:** OFICIAL
- **Uso:** Dados detalhados, fotos, propostas

#### Endpoints Úteis

```
GET /divulga/rest/v1/candidatura/listar/{ano}/{municipio}/{cargo}/{turno}/candidatos
GET /divulga/rest/v1/candidatura/buscar/foto/{ano}/{municipio}/{sqCandidato}
```

### Cámaras dos Deputados

- **URL:** https://dadosabertos.camara.leg.br/
- **Tipo:** API REST (JSON)
- **Nível:** INSTITUCIONAL
- **Uso:** Histórico de mandatos, votações, proposições

### Senado Federal

- **URL:** https://www12.senado.leg.br/dados-abertos
- **Tipo:** API REST (JSON)
- **Nível:** INSTITUCIONAL
- **Uso:** Histórico de mandatos, votações, proposições

### Assembleia Legislativa de SP

- **URL:** https://www.al.sp.gov.br/
- **Tipo:** Web scraping (não documentado)
- **Nível:** INSTITUCIONAL
- **Uso:** Dados de deputados estaduais

---

## Regras para Fontes

1. **Não inventar URLs** — Validar cada endpoint antes de usar
2. **Não assumir disponibilidade** — Tratar erros gracefully
3. **Documentar cada fonte** — Registrar data da última verificação
4. **Preferir fontes oficiais** — TSE é a fonte primária
5. **Não tratar fonte secundária como oficial** — Dados devem ser verificáveis
6. **Permitir falha parcial** — Uma fonte indisponível não deve parar o sistema

---

## Status das Fontes

| Fonte | Status | Última Verificação | Observação |
|-------|--------|-------------------|------------|
| TSE Dados Abertos | ⚠️ Requer navegador | 2026-09-21 | Bloqueio 403 para acesso automatizado |
| DivulgaCandContas | ⚠️ Requer navegador | 2026-09-21 | Bloqueio 403 para acesso automatizado |
| Câmara dos Deputados | ✅ API pública | — | Acesso livre |
| Senado Federal | ✅ API pública | — | Acesso livre |

---

## Formato do CSV de Importação

### Cabeçalho Obrigatório

```
ANO_ELEICAO;CD_TIPO_ELEICAO;NM_TIPO_ELEICAO;NR_TURNO;CD_ELEICAO;DS_ELEICAO;DT_ELEICAO;SG_UF;SG_UE;NM_UE;CD_CARGO;DS_CARGO;SQ_CANDIDATO;NR_CANDIDATO;NM_CANDIDATO;NM_URNA_CANDIDATO;NM_SOCIAL_CANDIDATO;NR_CPF_CANDIDATO;NM_EMAIL;CD_SITUACAO_CANDIDATURA;DS_SITUACAO_CANDIDATURA;CD_DETALHE_SITUACAO_CAND;DS_DETALHE_SITUACAO_CAND;TP_AGREMIACAO;NR_PARTIDO;SG_PARTIDO;NM_PARTIDO;SQ_COLIGACAO;NM_COLIGACAO;DS_COMPOSICAO_COLIGACAO;DT_ULTIMA_ATUALIZACAO
```

### Exemplo de Linha

```
2026;2;ORDINÁRIA;1;999;Eleições Gerais 2026;04/10/2026;SP;SP;SÃO PAULO;6;SENADOR;250001005001;100;CANDIDATO EXEMPLO;CANDIDATO EXEMPLO;#NULO#;-1;#NULO#;2;DEFERIDO;2;DEFERIDO;COLIGAÇÃO;10;PARTIDO EXEMPLO;Partido Exemplo;25000000001;COLIGAÇÃO EXEMPLO;PARTIDO A / PARTIDO B;2026-09-01 10:00:00
```

---

## Notas Importantes

1. **Dados fictícios são proibidos** — O PRD do projeto veta explicitamente a invenção de dados
2. **Apenas DEFERIDOS** — Candidatos INDEFERIDOS ou com pendências não devem ser importados
3. **Eleição de 2026** — Apenas dados da eleição de 04/10/2026
4. **Encoding** — Arquivos do TSE são ISO-8859-1; o importador converte para UTF-8
5. **Duplicatas** — O sistema previne duplicatas usando `tse_id` como chave única
