FROM php:8.2-fpm

WORKDIR /var/www

# Instalar dependências e extensões PHP
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    unzip \
    nodejs \
    npm \
    redis-server \
    supervisor \
    python3 \
    python3-venv \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar arquivos do projeto
COPY . /var/www/html

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Instalar dependências do projeto
WORKDIR /var/www/html
RUN composer install --no-interaction --no-plugins --no-scripts
RUN npm install
RUN npm run build

# Copiar configurações
COPY nginx.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# instalar e configurar mysql
RUN apt-get update && apt-get install -y mysql-server
RUN service mysql start
RUN mysql -u root -e "CREATE DATABASE IF NOT EXISTS localstackui"
RUN mysql -u root -e "CREATE USER IF NOT EXISTS 'localstackui'@'localhost' IDENTIFIED BY 'localstackui'"
RUN mysql -u root -e "GRANT ALL PRIVILEGES ON localstackui.* TO 'laravel'@'localhost'"
RUN mysql -u root -e "FLUSH PRIVILEGES"

# Configurar Redis
RUN sed -i 's/bind 127.0.0.1 ::1/bind 0.0.0.0/g' /etc/redis/redis.conf

# Copiar script de inicialização
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80 6001 6379

CMD ["/start.sh"]
