FROM php:8.2-fpm

WORKDIR /var/www

# Install dependencies and PHP extensions
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
    supervisor \
    sqlite3 \
    libsqlite3-dev \
    redis \
    && docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy project files
COPY . /var/www/html

# Set permissions
RUN chown -R root:root /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Install project dependencies
WORKDIR /var/www/html
RUN composer install --no-interaction --no-plugins --no-scripts
RUN npm install
RUN npm run build

# Copy configurations
COPY nginx.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create log directory and set permissions
RUN mkdir -p /var/log/php-fpm && chown -R www-data:www-data /var/log/php-fpm

# Copy startup script
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
