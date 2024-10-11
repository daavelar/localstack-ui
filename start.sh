#!/bin/sh

# Create database file
touch /var/www/html/database/database.sqlite

# Set permissions for the SQLite database file
chown -R root:root /var/www/html/database
chmod -R 777 /var/www/html/database
chmod -R 777 /var/www/html/storage

# Run migrations
php /var/www/html/artisan migrate

# Start supervisord
exec supervisord -c /etc/supervisor/supervisord.conf
