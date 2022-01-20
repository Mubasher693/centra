#!/bin/bash

docker-compose --env-file .env -f docker-compose.yml down
docker-compose --env-file .env -f docker-compose.yml up -d --build

[ -d "./vendor" ] && echo "Vendor exists." || docker-compose run php composer install
