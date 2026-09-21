#!/bin/bash
# =============================================================================
# Eleitor Consciente 2026 - Configuração de Cron Jobs
# Uso: sudo ./cron-setup.sh
# =============================================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

PROJECT_DIR="/var/www/eleitor-consciente"
SCRIPTS_DIR="$PROJECT_DIR/build/scripts"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Configuração de Cron Jobs              ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Verificar se é root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}[ERRO] Execute como root: sudo ./cron-setup.sh${NC}"
    exit 1
fi

# Backup do crontab atual
crontab -l > /tmp/crontab_backup_$(date +%Y%m%d) 2>/dev/null || true

# Criar crontab temporário
CRON_TEMP="/tmp/crontab_new"
cat > "$CRON_TEMP" << 'EOF'
# =============================================================================
# Eleitor Consciente 2026 - Cron Jobs
# =============================================================================

# Backup diário às 3h da manhã
0 3 * * * /var/www/eleitor-consciente/build/scripts/backup.sh >> /var/log/eleitor-backup.log 2>&1

# Limpeza de logs antigos (semanal, domingo às 4h)
0 4 * * 0 find /var/log/eleitor-* -name "*.log" -mtime +30 -delete 2>/dev/null

# Verificação de saúde do container (a cada 5 minutos)
*/5 * * * * docker inspect eleitor_php >/dev/null 2>&1 || (docker compose -f /var/www/eleitor-consciente/build/docker-compose.yml up -d 2>&1 | logger -t eleitor-healthcheck)

EOF

# Adicionar crontabs existentes (se houver)
crontab -l 2>/dev/null | grep -v "eleitor-consciente" | grep -v "Eleitor Consciente" >> "$CRON_TEMP" 2>/dev/null || true

# Instalar novo crontab
crontab "$CRON_TEMP"
rm -f "$CRON_TEMP"

echo -e "${GREEN}[OK] Cron jobs instalados:${NC}"
echo ""
crontab -l | grep -A1 "eleitor\|Eleitor"
echo ""
echo -e "${YELLOW}Logs:${NC}"
echo "  Backup: /var/log/eleitor-backup.log"
echo ""
echo -e "${YELLOW}Para verificar:${NC}"
echo "  crontab -l"
echo "  tail -f /var/log/eleitor-backup.log"
