# Guia Operacional - Eleitor Consciente 2026

## 1. Operações de Rotina e Importação

### 1.1 Iniciar a Aplicação
- Windows: executar `start.bat`
- Linux/Mac: `docker compose up -d`

### 1.2 Parar a Aplicação
- Windows: executar `stop.bat`
- Parar com limpeza de volumes de banco: `stop.bat clean`
- Linux/Mac: `docker compose down`

### 1.3 Executar Importações Manuais
Para importar um arquivo CSV do TSE:
```bash
docker compose exec php php importer/cli.php import --file=caminho/do/arquivo.csv
```

### 1.4 Verificar Status das Importações
Pela interface web:
- Acesse `http://localhost:8080/status`

Pelo terminal:
```bash
docker compose exec php php importer/cli.php status
```

Pela API REST:
```bash
curl http://localhost:8080/api/v1/imports/status
```

### 1.5 Monitoramento e Health Check
Acesse o endpoint de monitoramento:
```bash
curl http://localhost:8080/api/v1/health
```
Resposta esperada:
```json
{
  "success": true,
  "data": {
    "application": "ok",
    "database": "ok",
    "timestamp": "2026-09-21T09:00:00-03:00",
    "version": "1.0.0"
  }
}
```

