#!/bin/bash

# Executar migrações do Laravel
php artisan migrate --force

# Iniciar supervisord
/usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
