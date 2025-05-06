# Usa a imagem oficial do PHP 8.3 com FPM baseada no Alpine
FROM php:8.3-fpm-alpine

# Instala as dependências necessárias para Laravel com PostgreSQL e extensões do PHP
RUN apk --no-cache add \
    autoconf build-base libpq-dev libpng-dev libjpeg-turbo-dev freetype-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pgsql pdo_pgsql pdo gd \
    && apk add --no-cache openssl-dev libzip-dev \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instala o Composer (pego da imagem oficial mais atual)
COPY --from=composer:2.7.1 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Configurações do Laravel
RUN mv .env.docker .env
RUN php artisan key:generate
RUN php artisan route:cache
RUN php artisan config:cache

# Expõe a porta do PHP-FPM
EXPOSE 9000

# Altera a propriedade dos arquivos para o usuário do PHP-FPM
RUN chown -R www-data:www-data /var/www

# Comando padrão para iniciar o PHP-FPM
CMD ["php-fpm"]
