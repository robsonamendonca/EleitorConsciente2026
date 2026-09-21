# Diretrizes de Segurança - Eleitor Consciente 2026

## 1. Práticas de Segurança Implementadas

### 1.1 Prevenção de SQL Injection
- Todas as consultas ao banco de dados são executadas via **PDO com Prepared Statements** e bindings explícitos de parâmetros.
- O modo `PDO::ATTR_EMULATE_PREPARES` é desativado para garantir a preparação nativa de queries pelo motor MySQL.

### 1.2 Prevenção de Cross-Site Scripting (XSS)
- Todo output dinâmico gerado no backend é sanitizado via `htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
- O cliente JavaScript não utiliza `eval()` e manipula textos através de propriedades seguras.

### 1.3 Headers HTTP de Segurança
A aplicação envia por padrão:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: SAMEORIGIN`
- `X-XSS-Protection: 1; mode=block`
- `Referrer-Policy: strict-origin-when-cross-origin`

### 1.4 Proteção de Rotas Administrativas
- Endpoints administrativos de importação (`/api/v1/admin/...`) exigem autenticação via Bearer token ou query parameter comparado em tempo constante via `hash_equals()`.

### 1.5 Tratamento de Exceções em Produção
- O front controller intercepta erros não tratados e nunca expõe stack traces ou dados sensíveis de infraestrutura em modo de produção (`APP_DEBUG=false`).

