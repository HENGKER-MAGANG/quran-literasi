# Base image resmi PHP + Apache
FROM php:8.2-apache

# Aktifkan mod_rewrite (penting untuk banyak framework PHP)
RUN a2enmod rewrite

# Install ekstensi yang umum digunakan (opsional, bisa disesuaikan)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Salin semua file ke direktori root Apache
COPY . /var/www/html/

# Ubah permission (opsional, tergantung kebutuhan)
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port (default Apache)
EXPOSE 80
