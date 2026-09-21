#!/bin/bash
# =============================================================================
# Eleitor Consciente 2026 - Backup Automático do Banco
# Uso: ./backup.sh (configurar via cron)
# =============================================================================

set -e

# Configurações
BACKUP_DIR="${BACKUP_DIR:-/var/backups/eleitor_consciente}"
RETENTION_DAYS="${BACKUP_RETENTION_DAYS:-30}"
DB_HOST="${DB_HOST:-mysql}"
DB_USER="${DB_USERNAME:-eleitor_user}"
DB_PASS="${DB_PASSWORD:-eleitor_pass}"
DB_NAME="${DB_DATABASE:-eleitor_consciente}"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/eleitor_consciente_${DATE}.sql.gz"

# Criar diretório se não existir
mkdir -p "$BACKUP_DIR"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Iniciando backup do banco $DB_NAME..."

# Dump compactado
if docker exec eleitor_mysql mysqldump \
    -u"$DB_USER" -p"$DB_PASS" \
    --single-transaction \
    --routines \
    --triggers \
    --no-tablespaces \
    "$DB_NAME" 2>/dev/null | gzip > "$BACKUP_FILE"; then
    
    SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Backup concluído: $BACKUP_FILE ($SIZE)"
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ERRO: Falha ao criar backup!"
    rm -f "$BACKUP_FILE"
    exit 1
fi

# Limpar backups antigos
DELETED=$(find "$BACKUP_DIR" -name "eleitor_consciente_*.sql.gz" -mtime +$RETENTION_DAYS -delete -print | wc -l)
if [ "$DELETED" -gt 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Removidos $DELETED backups antigos (>$RETENTION_DAYS dias)"
fi

# Listar backups existentes
TOTAL=$(ls -1 "$BACKUP_DIR"/eleitor_consciente_*.sql.gz 2>/dev/null | wc -l)
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Total de backups: $TOTAL"
