# Politica de Seguranca - Eleitor Consciente 2026

## Reportando Vulnerabilidades

Se voce encontrar uma vulnerabilidade de seguranca, por favor **NAO** a reporte publicamente Issues. Em vez disso, envie um e-mail para:

**seguranca@eleitorconsciente.com.br**

Inclua:
- Descricao da vulnerabilidade
- Passos para reproduzir
- Potencial impacto
- Sugestao de correcao (se possivel)

Responderemos em ate 48 horas e trabalharemos juntos para corrigir o problema.

## Politica de Divulgacao

- Reportaremos vulnerabilidades confirmadas em ate 7 dias uteis
- Creditaremos o pesquisador que reportou (a menos que prefira anonimato)
- Nao tomaremos acoes legais contra pesquisadores que sigam esta politica

## Medidas de Seguranca Implementadas

### Dados e Armazenamento
- **Nao coletamos dados pessoais** (LGPD compliant)
- A Cola Eleitoral utiliza exclusivamente `localStorage` do navegador
- Nenhuma dado do usuario trafega pela internet ou e armazenado em servidores

### Infraestrutura
- Variaveis de ambiente para todas as credenciais (nunca hardcoded)
- Arquivos `.env` no `.gitignore` (nunca versionados)
- Headers de seguranca HTTP (CSP, X-Frame-Options, X-XSS-Protection)
- Permissoes restritivas em diretorios sensíveis
- Backup automatizado com criptografia

### Codigo
- Validacao de entrada em todas as entradas de dados
- Prepared statements para queries SQL (prevencao de SQL Injection)
- Escape de saida HTML (prevencao de XSS)
- Hash SHA-256 para auditoria de dados importados
- Code review antes de merges

### API
- Chave de administracao necessaria para endpoints de importacao
- Rate limiting recomendado em producao
- Logs de auditoria para todas as operacoes criticas

## Checklist de Seguranca para Deploy

- [ ] Altere todas as senhas padrao do `.env.example`
- [ ] Gere chaves aleatorias para `ADMIN_API_KEY` (`openssl rand -hex 32`)
- [ ] Configure `APP_ENV=production` e `APP_DEBUG=false`
- [ ] Habilite HTTPS (certificado SSL/TLS)
- [ ] Configure firewall (apenas portas 80/443/22)
- [ ] Restrinja acesso SSH (chaves SSH, desative login por senha)
- [ ] Configure backup automatizado
- [ ] Monitore logs de erro e acesso
