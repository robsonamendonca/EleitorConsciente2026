# Dicionário de Dados - Eleitor Consciente 2026

## 1. Tabelas Principais

### `elections`
Armazena as edições de eleições registradas.
- `id`: Chave primária inteira auto-incrementada.
- `year`: Ano da eleição (ex: 2026).
- `name`: Título descritivo oficial da eleição.
- `election_type`: Tipo do pleito (`GERAL`, `MUNICIPAL`, `SUPLEMENTAR`).
- `status`: Situação (`ativo`, `concluido`, `arquivado`).

### `offices`
Cargos em disputa no pleito eleitoral.
- `id`: Chave primária.
- `code`: Código mnemônico (`PRESIDENTE`, `GOVERNADOR`, `SENADOR`, `DEPUTADO_FEDERAL`, `DEPUTADO_ESTADUAL`).
- `name`: Nome legível do cargo.
- `level`: Âmbito de atuação (`FEDERAL`, `ESTADUAL`, `MUNICIPAL`).

### `parties`
Partidos políticos registrados perante o TSE.
- `id`: Chave primária.
- `acronym`: Sigla partidária oficial (ex: `PL`, `PT`, `REPUBLICANOS`).
- `name`: Nome por extenso do partido.
- `number`: Número da legenda partidária.

### `candidates`
Dados dos candidatos cadastrados no TSE.
- `id`: Chave primária interna.
- `election_id`: Referência à eleição.
- `tse_id`: Identificador oficial único gerado pelo TSE (`SQ_CANDIDATO`).
- `office_id`: Referência ao cargo disputado.
- `state_code`: UF da candidatura (`SP` ou `BR`).
- `ballot_number`: Número do candidato na urna (tratado como string).
- `ballot_name`: Nome de urna escolhido pelo candidato.
- `full_name`: Nome civil completo.
- `party_acronym`: Sigla do partido político.
- `federation_name`: Nome da federação ou coligação partidária, se houver.
- `registration_status`: Situação da candidatura perante a Justiça Eleitoral (ex: `DEFERIDO`, `INDEFERIDO`).
- `photo_url`: URL oficial da foto de campanha no TSE.
- `source_last_updated_at`: Data da última atualização informada pela fonte original.

### `candidate_sources`
Rastreamento e auditoria da origem de cada dado de candidatura.
- `id`: Chave primária.
- `candidate_id`: ID do candidato.
- `source_id`: ID da fonte oficial em `sources`.
- `retrieved_at`: Data/hora em que a informação foi coletada.
- `content_hash`: Hash SHA-256 do arquivo fonte original.

### `data_imports` & `import_errors`
Tabelas de auditoria do pipeline de ingestão.
- `data_imports`: Registra cada lote processado com `file_hash`, contagens de `records_processed`, `records_inserted`, `records_updated`, `records_rejected` e `status`.
- `import_errors`: Registra linha, campo e mensagem de erro específica de qualquer linha que falhe na validação.

