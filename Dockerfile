# Use a imagem oficial do PHP com Apache
FROM php:8.1-apache

# Instale as extensões necessárias para o MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copie os arquivos do projeto para o diretório root do Apache
COPY . /var/www/html/

# Dê permissões para o diretório do Apache
RUN chown -R www-data:www-data /var/www/html