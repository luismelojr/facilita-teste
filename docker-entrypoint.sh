#!/bin/bash
set -e

# Copiar .env.example se .env não existir
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Gerar chave da aplicação
php artisan key:generate

# Rodar migrações
php artisan migrate --force

# Limpar cache
php artisan config:clear
php artisan cache:clear

# Gerar documentação Swagger
php artisan l5-swagger:generate

# Executar comandos passados
exec "$@"
