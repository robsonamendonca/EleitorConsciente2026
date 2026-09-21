FROM php:8.2-apache

# Atualiza pacotes e instala dependências básicas
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Ativa módulo rewrite do Apache
RUN a2enmod rewrite

# Copia Composer oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configura o Apache DocumentRoot para a pasta public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Habilita AllowOverride All para o .htaccess funcionar em public/
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && sed -i '/<Directory \${APACHE_DOCUMENT_ROOT}>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Cria diretório público para fotos (symlinks criados no entrypoint após montar volumes)
RUN mkdir -p /var/www/html/public/fotos /var/www/html/public/fotos_br

# Copia entrypoint para criar symlinks das fotos
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"]

