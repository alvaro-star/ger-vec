# Usa a imagem oficial do PHP 8.3 com FPM
FROM php:8.3-fpm

# Instala extensões necessárias para Laravel com PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev libpng-dev libjpeg-dev libfreetype6-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pgsql pdo_pgsql pdo

# Instala o Composer (pego da imagem oficial mais atual)
COPY --from=composer:2.7.1 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader


RUN mv .env.docker .env
RUN php artisan key:generate
RUN php artisan route:cache
RUN php artisan config:cache


# Expõe a porta do PHP-FPM
EXPOSE 9000

# Comando padrão para iniciar o FPM
RUN chown -R www-data:www-data \
    /var/www
CMD ["php-fpm"]
