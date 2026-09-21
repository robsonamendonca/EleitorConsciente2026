#!/bin/bash
# =============================================================================
# Eleitor Consciente 2026 - Setup Inicial VPS (Ubuntu/Debian)
# Uso: sudo ./setup-vps.sh
# =============================================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Setup VPS - Eleitor Consciente 2026   ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# 1. Atualizar sistema
echo -e "${YELLOW}[1/8] Atualizando sistema...${NC}"
apt-get update -qq && apt-get upgrade -y -qq

# 2. Instalar Docker
echo -e "${YELLOW}[2/8] Instalando Docker...${NC}"
if ! command -v docker &> /dev/null; then
    curl -fsSL https://get.docker.com | sh
    systemctl enable docker
    systemctl start docker
    echo -e "${GREEN}  Docker instalado!${NC}"
else
    echo -e "${GREEN}  Docker já instalado${NC}"
fi

# 3. Instalar Docker Compose
echo -e "${YELLOW}[3/8] Verificando Docker Compose...${NC}"
if ! docker compose version &> /dev/null; then
    apt-get install -y docker-compose-plugin
    echo -e "${GREEN}  Docker Compose instalado!${NC}"
else
    echo -e "${GREEN}  Docker Compose já instalado${NC}"
fi

# 4. Instalar utilitários
echo -e "${YELLOW}[4/8] Instalando utilitários...${NC}"
apt-get install -y -qq git curl wget nano htop unzip

# 5. Configurar firewall
echo -e "${YELLOW}[5/8] Configurando firewall...${NC}"
if command -v ufw &> /dev/null; then
    ufw allow 22/tcp    # SSH
    ufw allow 80/tcp    # HTTP
    ufw allow 443/tcp   # HTTPS
    ufw --force enable
    echo -e "${GREEN}  Firewall configurado!${NC}"
else
    echo -e "${YELLOW}  UFW não encontrado. Configure o firewall manualmente.${NC}"
fi

# 6. Criar diretório do projeto
echo -e "${YELLOW}[6/8] Preparando diretório do projeto...${NC}"
PROJECT_DIR="/var/www/eleitor-consciente"
mkdir -p "$PROJECT_DIR"
mkdir -p /var/backups/eleitor_consciente

# 7. Clonar repositório (se não existir)
if [ ! -d "$PROJECT_DIR/.git" ]; then
    echo -e "${YELLOW}  Clonando repositório...${NC}"
    git clone https://github.com/robsonamendonca/EleitorConsciente2026.git "$PROJECT_DIR"
else
    echo -e "${GREEN}  Repositório já existe${NC}"
    cd "$PROJECT_DIR" && git pull origin main
fi

# 8. Configurar .env
if [ ! -f "$PROJECT_DIR/.env" ]; then
    echo -e "${YELLOW}[7/8] Criando arquivo .env...${NC}"
    cp "$PROJECT_DIR/build/.env.production" "$PROJECT_DIR/.env"
    
    # Gerar senhas aleatórias
    DB_PASS=$(openssl rand -base64 24 | tr -d '/' | head -c 20)
    DB_ROOT=$(openssl rand -base64 24 | tr -d '/' | head -c 20)
    
    sed -i "s/ALTERE_PARA_UMA_SENHA_FORTE_AQUI/$DB_PASS/" "$PROJECT_DIR/.env"
    sed -i "s/ALTERE_PARA_UMA_SENHA_ROOT_FORTE_AQUI/$DB_ROOT/" "$PROJECT_DIR/.env"
    
    echo -e "${GREEN}  .env criado com senhas geradas!${NC}"
else
    echo -e "${GREEN}  .env já existe${NC}"
fi

# 9. Configurar permissões
echo -e "${YELLOW}[8/8] Configurando permissões...${NC}"
chown -R www-data:www-data "$PROJECT_DIR" 2>/dev/null || true
chmod -R 755 "$PROJECT_DIR" 2>/dev/null || true

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Setup concluído com sucesso!          ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "  Próximos passos:"
echo -e "  1. ${YELLOW}cd $PROJECT_DIR${NC}"
echo -e "  2. ${YELLOW}nano .env${NC} (verificar senhas e domínio)"
echo -e "  3. ${YELLOW}./build/scripts/deploy.sh${NC}"
echo ""
