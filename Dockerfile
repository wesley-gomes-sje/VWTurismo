# Use a imagem oficial do PHP com Apache
FROM php:8.3-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instale dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Habilite o mod_rewrite para URLs amigáveis
RUN a2enmod rewrite

# Copie os arquivos do projeto para o diretório root do Apache
COPY . /var/www/html/

# Dê permissões para o diretório do Apache
RUN chown -R www-data:www-data /var/www/html