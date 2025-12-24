.PHONY: up down build rebuild bash migrate fresh seed test logs logs-app cache-clear setup restart

# Subir containers
up:
	docker compose up -d

# Derrubar containers
down:
	docker compose down

# Build das imagens
build:
	docker compose build

# Rebuild forçado (sem cache)
rebuild:
	docker compose build --no-cache

# Acesso ao bash do container app
bash:
	docker compose exec app bash

# Rodar migrations
migrate:
	docker compose exec app php artisan migrate --force

# Fresh migrations + seed
fresh:
	docker compose exec app php artisan migrate:fresh --seed --force

# Seed
seed:
	docker compose exec app php artisan db:seed --force

# Testes
test:
	docker compose exec app php artisan test

# Logs de todos os containers
logs:
	docker compose logs -f --tail=200

# Logs apenas do app
logs-app:
	docker compose logs -f --tail=200 app

# Limpar cache
cache-clear:
	docker compose exec app php artisan optimize:clear

# Setup completo (build + up + migrate + seed)
setup: build up
	@echo "Aguardando serviços ficarem healthy..."
	@until docker compose exec app php artisan migrate --force 2>/dev/null; do \
		echo "Aguardando app..."; \
		sleep 3; \
	done
	@docker compose exec app php artisan db:seed --force
	@echo ""
	@echo "=========================================="
	@echo "  Setup completo!"
	@echo "  API: http://localhost:8000"
	@echo "  Adminer: http://localhost:8080"
	@echo "=========================================="

# Restart dos containers
restart: down up

# Status dos containers
status:
	docker compose ps

# Horizon (queue worker)
horizon:
	docker compose exec queue php artisan horizon

# Swagger UI
swagger:
	docker compose --profile tools up -d swagger
