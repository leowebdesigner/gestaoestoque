.PHONY: up down build bash migrate seed test queue schedule logs cache-clear init swagger

up:
	docker compose up -d --build

down:
	docker compose down

build:
	docker compose build

bash:
	docker compose exec app bash

migrate:
	docker compose exec app sh -lc 'while [ ! -f /var/www/html/vendor/autoload.php ]; do sleep 2; done; while ! php -r "new PDO(\"mysql:host=mysql;port=3306;dbname=cplug\", \"cplug\", \"cplug\");" >/dev/null 2>&1; do sleep 2; done; while ! curl -fsS http://localhost:8000/health >/dev/null 2>&1; do sleep 2; done; php artisan migrate'

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

swagger:
	docker compose up -d swagger

init:
	docker compose run --rm --entrypoint "" app composer create-project laravel/laravel .
