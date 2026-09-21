#!/bin/bash
set -e

# Cria symlinks para fotos após montar volumes
# Os volumes são montados DEPOIS do build, então os symlinks devem ser criados aqui

# Fotos SP (estadual)
if [ -d "/var/www/html/database/foto_cand2026_SP_div" ]; then
    rm -rf /var/www/html/public/fotos
    ln -sf /var/www/html/database/foto_cand2026_SP_div /var/www/html/public/fotos
    echo "[entrypoint] Symlink fotos SP criado"
fi

# Fotos BR (nacional)
if [ -d "/var/www/html/database/foto_cand2026_BR_div" ]; then
    rm -rf /var/www/html/public/fotos_br
    ln -sf /var/www/html/database/foto_cand2026_BR_div /var/www/html/public/fotos_br
    echo "[entrypoint] Symlink fotos BR criado"
fi

# Inicia o Apache (comando padrão do php:8.2-apache)
exec apache2-foreground
