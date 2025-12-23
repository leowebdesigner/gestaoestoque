# CPlug ERP - API de Estoque e Vendas

API REST de controle de estoque e vendas para um ERP.

## Requisitos
- Docker e Docker Compose
- Make

## Subindo o ambiente
Tudo roda dentro do Docker. Um comando para subir:

```bash
make up
```

Primeira vez (somente se ainda nao tiver criado o projeto):

```bash
make init
```

## URLs
- API: `http://localhost:8000`
- Adminer (DB): `http://localhost:8080`

## Credenciais do banco
- Host/servidor: `mysql`
- Porta: `3306`
- Database: `cplug`
- User: `cplug`
- Password: `cplug`

No Adminer, use Host `mysql`, User `cplug`, Password `cplug`, Database `cplug`.

## Comandos Make
Comandos principais:

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
```

## Fila e agendador
Containers dedicados:
- `queue`: worker de filas com Redis
- `scheduler`: `schedule:work` rodando comandos agendados

## Observacoes
- `.env` e `.env.example` ja configurados para MySQL + Redis.
- Session e cache em Redis.
