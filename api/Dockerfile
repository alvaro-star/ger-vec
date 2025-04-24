# Usa a imagem oficial do PHP 8.3 com FPM
FROM php:8.3-fpm

# Instala extensões necessárias para Laravel com PostgreSQL
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# Instala o Composer (pego da imagem oficial mais atual)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expõe a porta do PHP-FPM
EXPOSE 9000

# Comando padrão para iniciar o FPM
CMD ["php-fpm"]
