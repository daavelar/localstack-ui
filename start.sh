#!/bin/bash

# Iniciar MySQL
service mysql start

# Esperar MySQL iniciar completamente
while ! mysqladmin ping -h"localhost" --silent; do
    sleep 1
done

# Iniciar Redis
redis-server /etc/redis/redis.conf --daemonize yes

# Executar migrações do Laravel
php artisan migrate --force

# Iniciar supervisord
/usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
