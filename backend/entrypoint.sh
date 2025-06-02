#!/bin/bash

# Roda migrations se desejar
php artisan migrate --force

exec "$@"
