#!/bin/bash
set -e

# Cria symlinks para fotos após montar volumes
if [ -d "/var/www/html/database/foto_cand2026_SP_div" ]; then
    rm -rf /var/www/html/public/fotos
    ln -sf /var/www/html/database/foto_cand2026_SP_div /var/www/html/public/fotos
    echo "[entrypoint] Symlink fotos SP criado"
fi

if [ -d "/var/www/html/database/foto_cand2026_BR_div" ]; then
    rm -rf /var/www/html/public/fotos_br
    ln -sf /var/www/html/database/foto_cand2026_BR_div /var/www/html/public/fotos_br
    echo "[entrypoint] Symlink fotos BR criado"
fi

# Permissões
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true

exec apache2-foreground
