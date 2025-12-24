.PHONY: up down build bash migrate seed test queue schedule logs cache-clear init

up:
	docker compose up -d --build

down:
	docker compose down

build:
	docker compose build

bash:
	docker compose exec app bash

migrate:
	docker compose exec app php artisan migrate

seed:
	docker compose exec app php artisan db:seed

test:
	docker compose exec app php artisan test

queue:
	docker compose exec queue php artisan horizon

schedule:
	docker compose exec scheduler php artisan schedule:work

logs:
	docker compose logs -f --tail=200

cache-clear:
	docker compose exec app php artisan cache:clear

init:
	docker compose run --rm --entrypoint "" app composer create-project laravel/laravel .
