#!/bin/bash
set -e

echo "Installing Sail..."
composer install

echo "Fill the .env file..."
cp .env.example .env

echo "Starting Sail..."
./vendor/bin/sail up -d

echo "Generate a key..."
./vendor/bin/sail artisan key:generate

echo "Running migrations..."
./vendor/bin/sail artisan migrate:refresh

echo "Seeding the database..."
./vendor/bin/sail artisan db:seed
