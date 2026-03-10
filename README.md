# VWTurismo

Sistema de compra de passagens de ônibus desenvolvido em PHP 8 MVC sem framework.

Dois perfis de usuário:
- **admin** — gerencia cidades, veículos, rotas e visualiza todas as passagens e clientes
- **cliente** — compra passagens entre cidades e consulta suas próprias passagens

---

## Pré-requisitos

- [Docker](https://www.docker.com/) e Docker Compose instalados

---

## Passo a passo para rodar o projeto

### 1. Clone o repositório

```bash
git clone https://github.com/wesley-gomes-sje/VWTurismo.git
cd VWTurismo
```

### 2. Configure as variáveis de ambiente

Copie o arquivo de exemplo e preencha com suas credenciais:

```bash
cp .env.example .env
```

Edite o `.env` com os valores desejados:

```env
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=vwturismo
MYSQL_USER=vwuser
MYSQL_PASSWORD=vwpass
MYSQL_DB_HOST=db
```

> `MYSQL_DB_HOST` deve ser `db` (nome do serviço no Docker Compose).

### 3. Suba os containers

```bash
docker-compose up -d
```

Aguarde até o container do banco de dados estar saudável. Pode verificar com:

```bash
docker-compose ps
```

### 4. Instale as dependências PHP

```bash
docker exec vw_turismo composer install
```

### 5. Execute as migrations

```bash
docker exec vw_turismo php database/create_all_tables.php
```

Saída esperada:

```
Iniciando a criação de todas as tabelas...
Todas as tabelas foram criadas com sucesso.
```

### 6. Acesse a aplicação

Abra no navegador: [http://localhost:8080](http://localhost:8080)

---

## Comandos úteis

| Ação | Comando |
|------|---------|
| Subir os containers | `docker-compose up -d` |
| Parar os containers | `docker-compose down` |
| Entrar no container da aplicação | `docker exec -it vw_turismo bash` |
| Ver logs da aplicação | `docker-compose logs -f app` |
| Ver logs do banco | `docker-compose logs -f db` |

---

## Testes

Os testes unitários rodam dentro do container:

```bash
docker exec vw_turismo ./vendor/bin/phpunit --no-coverage
```

Ou localmente, se tiver PHP 8.3+ e Composer instalados:

```bash
composer install
./vendor/bin/phpunit --no-coverage
```

---

## Stack

| Item | Detalhe |
|------|---------|
| Linguagem | PHP 8 (sem framework) |
| Servidor | Apache (via Docker) |
| Banco de dados | MySQL 8.0 |
| Autenticação | Session (`$_SESSION`) |
| Dependências | `vlucas/phpdotenv` |
| Testes | PHPUnit 11 |
