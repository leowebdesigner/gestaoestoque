# CPlug ERP - API de Estoque e Vendas

API REST para controle de estoque e vendas em ERP

## Requisitos
- Docker e Docker Compose
- Make

## Subindo do zero (passo a passo)

1) Copiar o arquivo de ambiente
```bash
cp .env.example .env 
```
após por favor recarregue o env digitando comando cd . no terminal

2) Build e subir containers
```bash
make up
```

3) Rodar migrations
```bash
make migrate
```

4) Rodar seed (produtos e usuario de teste)
```bash
make seed
```

5) (Opcional) Rodar testes
```bash
make test
```

6) (Opcional) Subir Swagger UI
```bash
make swagger
```

## URLs
- API: `http://localhost:8000`
- Horizon: `http://localhost:8000/horizon`
- Swagger UI: `http://localhost:8081`
- Adminer (DB): `http://localhost:8080`

## Credenciais do banco
- Host/servidor: `mysql`
- Porta: `3306`
- Database: `cplug`
- User: `cplug`
- Password: `cplug`

## Usuário de teste
- Email: `test@example.com`
- Password: `password`

## Comandos Make
```bash
make up
make down
make build
make bash
make migrate
make seed
make test
make queue
make schedule
make logs
make cache-clear
make swagger
```

## Endpoints
- POST `/api/auth/login`
- POST `/api/auth/logout`
- POST `/api/inventory`
- GET `/api/inventory`
- POST `/api/sales`
- GET `/api/sales/{id}`
- GET `/api/reports/sales`

## Documentação
- OpenAPI: `docs/openapi.yaml`
- Postman:
  - `docs/postman/CPlug.postman_collection.json`
  - `docs/postman/CPlug.postman_environment.json`
  - O container do Swagger e usado para abrir a UI localmente sem precisar instalar ferramentas no host.

## Fila e agendador
Containers dedicados:
- `queue`: Horizon + Redis
- `scheduler`: `schedule:work`

## Observações
- `.env` e `.env.example` configurados para MySQL + Redis.
- Cache e session em Redis.
- Horizon liberado em ambiente local via `HORIZON_MIDDLEWARE=web`.
