# LOTR API

## Installation

- Clone the repo
- Copy .env.example to .env
- Docker compose up
- Connect to docker container (depends on your OS)
- Install packages
- Migrate and seed

```bash
git clone https://github.com/Ralonsogit/lotr-api.git
docker compose up -d
docker-compose exec php sh
Composer install
php artisan migrate –seed
```

There is a custom artisan command which truncate users, factions, equipments and characters tables, and seed them. Just in case you need it.

```bash
php artisan db:truncate-seed
```

CI/CD: When you push into the repo there is a docker image that is updated in github container registry

## Documentation

http://localhost:8080/swagger

## Postman collection

Import this collection into your postman
/LOTR.postman_collection

## Performs

- Jobs and queues with supervisord
- oAuth2 (Laravel/passport)
- Monitoring tool cache (Laravel Telescope)
- Update docker to copy .env, install composer and project, migrate when running the docker image
- Add feature tests
- Get unit tests better
- Manage user roles properly with spatie/laravel-permission
- CI/CD for production server
- Better exceptions handling

## Packages used

- reliese/laravel
- laravel/sanctum
- darkaonline/l5-swagger
- wotz/laravel-swagger-ui
- zircote/swagger-php
- predis/predis
- fakerphp/faker
- phpunit/phpunit
