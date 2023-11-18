#!/bin/bash

echo "Deployement Symfony"
docker compose run --rm sf composer install

# Migrations Doctrines
docker compose exec sf php bin/console doctrine:migrations:list
docker compose exec sf php bin/console doctrine:migrations:migrate

echo "Install des packages node"
docker compose run --rm encore npm ci

echo "Compilation des Assets"
docker compose run --rm encore npm run dev
