#!/bin/bash
set -e

echo "Starting Sail..."
./vendor/bin/sail up -d

echo "Running migrations..."
./vendor/bin/sail artisan migrate

echo "Seeding the database..."
./vendor/bin/sail artisan db:seed
