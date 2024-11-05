#!/bin/bash
set -e

echo "Installing Sail..."
./vendor/bin/sail composer install

echo "Fill the .env file..."
cp .env.example .env

echo "Generate a key..."
./vendor/bin/sail artisan key:generate

echo "Starting Sail..."
./vendor/bin/sail up -d

echo "Running migrations..."
./vendor/bin/sail artisan migrate

echo "Seeding the database..."
./vendor/bin/sail artisan db:seed
