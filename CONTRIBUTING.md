# Guia de Contribuicao - Eleitor Consciente 2026

Obrigado por contribuir com o Eleitor Consciente 2026! Este projeto e uma iniciativa de tecnologia civica dedicada a fornecer informacoes eleitorais transparentes, acessiveis e auditaveis.

## Como Contribuir

### Reportar Bugs

1. Abra uma Issue no GitHub descrevendo o problema
2. Inclua passos para reproduzir
3. Informe o navegador e sistema operacional utilizado
4. Se possivel, anexe screenshots

### Sugerir Melhorias

1. Abra uma Issue com a tag `enhancement`
2. Descreva claramente a funcionalidade desejada
3. Explique o beneficio para os eleitores

### Contribuir com Codigo

1. Fork o repositorio
2. Crie uma branch para sua feature (`git checkout -b feature/minha-feature`)
3. Commit suas mudancas (`git commit -m 'Adiciona minha feature'`)
4. Push para a branch (`git push origin feature/minha-feature`)
5. Abra um Pull Request

### Padroes de Codigo

- **PHP**: PSR-12 (Coding Style)
- **JavaScript**: ES6+ sem frameworks pesados
- **CSS**: BEM ou utility-first, mobile-first
- **Commits**: Mensagens claras em portugues ou ingles

### Estrutura de Pastas

```
src/              # Codigo PHP (Domain, Http, Support)
public/           # Assets estaticos (CSS, JS, imagens)
views/            # Templates PHP
database/         # Migrations, seeds, fixtures
importer/         # Pipeline de importacao de dados
tests/            # Testes unitarios e de integracao
docs/             # Documentacao do projeto
build/            # Artefatos de deploy
```

### Regras Importantes

1. **Nao invente dados**: Todo dado deve ter fonte oficial (TSE/TRE)
2. **Nao recomende candidatos**: O projeto e neutro e imparcial
3. **Privacidade primeiro**: Nao colete dados pessoais dos usuarios
4. **Acessibilidade**: Use HTML semantico e cores com contraste adequado

## Contribuindo com Dados

### Atualizacao de Candidatos

1. Baixe os CSVs do TSE (Consulta Candidatos 2026)
2. Coloque em `database/fixtures/`
3. Execute: `docker exec php php importer/cli.php import-all`
4. Verifique: `docker exec php php importer/cli.php stats`

### Correcao de Dados

Se encontrar dados incorretos nos CSVs do TSE:
1. Abra uma Issue documentando o erro
2. Inclua a fonte oficial que comprova a correcao
3. Aguardaremos a atualizacao oficial do TSE

## Codigo de Conduta

- Respeite todos os participantes
- Fique focado no impacto civico do projeto
- Evite discussoes politicas partidarias
- Mantenha o debate saudavel e construtivo

## Perguntas?

Abra uma Issue com a tag `question` ou entre em contato pela equipe do projeto.
