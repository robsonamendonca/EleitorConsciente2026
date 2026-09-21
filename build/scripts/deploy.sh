#!/bin/bash
# =============================================================================
# Eleitor Consciente 2026 - Script de Deploy
# Uso: ./deploy.sh
# =============================================================================

set -e

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_DIR="$(cd "$(dirname "$0")/../.." && pwd)"
BUILD_DIR="$(cd "$(dirname "$0")/.." && pwd)"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Eleitor Consciente 2026 - Deploy      ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# 1. Verificar se .env existe
if [ ! -f "$PROJECT_DIR/.env" ]; then
    echo -e "${YELLOW}[AVISO] Arquivo .env não encontrado. Copiando template...${NC}"
    cp "$BUILD_DIR/.env.production" "$PROJECT_DIR/.env"
    echo -e "${RED}[ATENÇÃO] Edite o arquivo .env com suas credenciais antes de continuar!${NC}"
    echo "  nano $PROJECT_DIR/.env"
    exit 1
fi

# 2. Carregar variáveis
source "$PROJECT_DIR/.env"

echo -e "${YELLOW}[1/6] Verificando dependências...${NC}"
command -v docker >/dev/null 2>&1 || { echo -e "${RED}Docker não encontrado!${NC}"; exit 1; }
command -v docker compose >/dev/null 2>&1 || { echo -e "${RED}Docker Compose não encontrado!${NC}"; exit 1; }

echo -e "${YELLOW}[2/6] Parando containers anteriores...${NC}"
cd "$PROJECT_DIR"
docker compose -f build/docker-compose.yml down 2>/dev/null || true

echo -e "${YELLOW}[3/6] Buildando imagem...${NC}"
docker compose -f build/docker-compose.yml build --no-cache

echo -e "${YELLOW}[4/6] Subindo containers...${NC}"
docker compose -f build/docker-compose.yml up -d

echo -e "${YELLOW}[5/6] Aguardando MySQL ficar pronto...${NC}"
sleep 15

# Verificar se MySQL está pronto
for i in {1..30}; do
    if docker exec eleitor_mysql mysqladmin ping -h localhost -u root -p"${DB_ROOT_PASSWORD}" --silent 2>/dev/null; then
        echo -e "${GREEN}  MySQL pronto!${NC}"
        break
    fi
    sleep 2
done

echo -e "${YELLOW}[6/6] Verificando schema do banco...${NC}"
TABLES=$(docker exec eleitor_mysql mysql -uroot -p"${DB_ROOT_PASSWORD}" -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_DATABASE}'" 2>/dev/null || echo "0")

if [ "$TABLES" -eq "0" ]; then
    echo "  Schema vazio. Executando migrações e seeds..."
    docker exec eleitor_php php importer/cli.php migrate
    docker exec eleitor_php php importer/cli.php seed
    echo -e "${GREEN}  Schema criado com sucesso!${NC}"
else
    echo -e "${GREEN}  Schema já existe ($TABLES tabelas)${NC}"
fi

# Verificar candidatos
CANDIDATES=$(docker exec eleitor_mysql mysql -uroot -p"${DB_ROOT_PASSWORD}" -N -e "SELECT COUNT(*) FROM ${DB_DATABASE}.candidates" 2>/dev/null || echo "0")
echo -e "  Candidatos no banco: ${GREEN}$CANDIDATES${NC}"

if [ "$CANDIDATES" -eq "0" ]; then
    echo -e "${YELLOW}  Importando dados dos candidatos...${NC}"
    docker exec eleitor_php php importer/cli.php import-all
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Deploy concluído com sucesso!         ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "  URL: ${YELLOW}http://localhost:${APP_PORT:-8080}${NC}"
echo -e "  Logs: ${YELLOW}docker compose -f build/docker-compose.yml logs -f${NC}"
echo ""
