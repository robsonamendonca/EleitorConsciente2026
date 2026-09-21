# Changelog - Eleitor Consciente 2026

Todas as alterações relevantes neste projeto serão documentadas neste arquivo.

## [1.0.0] - 2026-09-21
### Adicionado
- Fundação completa da aplicação web mobile-first em PHP 8.2 e MySQL 8.0.
- Ambiente Docker e Docker Compose turnkey com scripts `start.bat` e `stop.bat`.
- Migrações de banco e seeds para Eleições 2026, Cargos oficiais, Partidos e Fontes oficiais.
- Módulo de importação em lotes (`importer/`) com adaptador do TSE, normalizador, validador de campos e runner CLI.
- API REST com endpoints públicos (`/api/v1/...`), paginação e health check (`/api/v1/health`).
- Interface do usuário acessível e responsiva: busca de candidatos, filtros por cargo e partido, páginas de detalhes com fontes rastreáveis.
- Ferramenta da "Cola Eleitoral" com armazenamento 100% local no navegador (`localStorage`), conformidade estrita com a LGPD e layout otimizado para impressão.
- Testes automatizados unitários e de integração com PHPUnit.
- Suíte completa de documentação (`docs/`) e pipeline de CI no GitHub Actions.

