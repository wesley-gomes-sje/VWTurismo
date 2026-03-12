# VWTurismo

Sistema web completo para compra e gestão de passagens de ônibus, desenvolvido com arquitetura **Backend API REST (PHP 8.3)** + **Frontend SPA (React 18)**.

---

## Visão Geral

Dois perfis de usuário:

- **admin** — gerencia cidades, veículos, rotas, visualiza todas as passagens e clientes
- **cliente** — compra passagens entre cidades e consulta suas próprias passagens

---

## Arquitetura

```
VWTurismo/
├── backend/    ← API REST PHP 8.3 (sem framework)
└── frontend/   ← SPA React 18 + Vite 5
```

### Backend

MVC artesanal em PHP 8.3, sem dependência de framework. Toda comunicação com o frontend é via JSON.

```
backend/
├── index.php                     ← Entry point: bifurca /api/* (JWT) e /* (Session)
├── Router.php                    ← Roteador com suporte a parâmetros dinâmicos {id}
├── routes/
│   ├── api.php                   ← Mapa RESTful dos endpoints da API
│   └── web.php                   ← Rotas legacy (admin PHP/HTML)
├── Controller/
│   └── Api/
│       ├── ApiController.php     ← Base abstrata com helpers json/success/error
│       ├── AuthApiController.php ← Login e registro
│       ├── CityApiController.php ← CRUD de cidades
│       ├── VehicleApiController.php
│       ├── RouteApiController.php
│       ├── TicketApiController.php
│       ├── UserApiController.php
│       └── DocsController.php    ← Swagger UI + openapi.json
├── Model/                        ← Acesso ao banco via PDO (injeção de dependência)
├── services/
│   └── JwtService.php            ← Geração e validação de tokens JWT
├── middlewares/
│   ├── JwtMiddleware.php         ← Valida Bearer token no header Authorization
│   ├── CorsMiddleware.php        ← Headers CORS para consumo pelo frontend
│   └── AuthMiddleware.php        ← Middleware de sessão (rotas legacy)
├── database/
│   └── create_all_tables.php     ← Migrations + seed do admin
└── tests/
    └── Unit/                     ← 151 testes unitários (PHPUnit 11)
```

**Autenticação:** JWT (HS256) via `firebase/php-jwt ^7.0`. Token enviado no header `Authorization: Bearer <token>`.

**Preço das passagens:** calculado automaticamente com base na distância da rota × R$ 0,50/km.

### Frontend

SPA React 18 consumindo a API REST via Axios. Estado de autenticação gerenciado com Zustand.

```
frontend/
├── src/
│   ├── api/
│   │   ├── client.js        ← Axios com interceptor JWT e redirect no 401
│   │   ├── auth.js
│   │   ├── cities.js
│   │   ├── vehicles.js
│   │   ├── routes.js
│   │   ├── tickets.js
│   │   └── users.js
│   ├── store/
│   │   └── authStore.js     ← Zustand: user, token, setAuth, logout
│   ├── components/
│   │   ├── layout/          ← Layout, Navbar, Sidebar
│   │   └── ui/              ← Button, Card, Alert (componentes reutilizáveis)
│   ├── pages/
│   │   ├── auth/            ← LoginPage, RegisterPage
│   │   ├── admin/           ← CitiesPage, VehiclesPage, RoutesPage,
│   │   │                       AdminTicketsPage, UsersPage
│   │   └── customer/        ← TicketsPage, BuyTicketPage
│   └── App.jsx              ← Rotas protegidas com React Router DOM
└── vite.config.js           ← Proxy /api → localhost:8080
```

---

## Stack

| Camada | Tecnologia |
|--------|-----------|
| Backend | PHP 8.3 (sem framework) |
| Servidor | Apache 2.4 (via Docker) |
| Banco de dados | MySQL 8.0 |
| Autenticação API | JWT HS256 (`firebase/php-jwt ^7.0`) |
| Documentação API | Swagger UI (`zircote/swagger-php ^6.0`) |
| Testes | PHPUnit 11 (151 testes unitários) |
| Frontend | React 18 + Vite 5 |
| Estilização | Tailwind CSS v3 |
| Estado global | Zustand |
| Requisições HTTP | Axios |
| Cache de servidor | TanStack Query (React Query) |
| Roteamento SPA | React Router DOM v6 |

---

## Endpoints da API

| Método | URI | Acesso | Descrição |
|--------|-----|--------|-----------|
| POST | `/api/auth/login` | Público | Autentica e retorna JWT |
| POST | `/api/auth/register` | Público | Cria conta de cliente |
| GET | `/api/cities` | Autenticado | Lista cidades ativas |
| POST | `/api/cities` | Admin | Cria cidade |
| PUT | `/api/cities/{id}` | Admin | Atualiza cidade |
| DELETE | `/api/cities/{id}` | Admin | Soft delete cidade |
| GET | `/api/vehicles` | Autenticado | Lista veículos |
| POST | `/api/vehicles` | Admin | Cria veículo |
| PUT | `/api/vehicles/{id}` | Admin | Atualiza veículo |
| DELETE | `/api/vehicles/{id}` | Admin | Remove veículo |
| GET | `/api/routes` | Autenticado | Lista rotas |
| POST | `/api/routes` | Admin | Cria rota |
| PUT | `/api/routes/{id}` | Admin | Atualiza rota |
| DELETE | `/api/routes/{id}` | Admin | Remove rota |
| GET | `/api/tickets` | Autenticado | Lista passagens (admin=todas, cliente=próprias) |
| POST | `/api/tickets` | Autenticado | Compra passagem |
| GET | `/api/users` | Admin | Lista clientes |
| GET | `/api/docs` | Público | Swagger UI |
| GET | `/api/openapi.json` | Público | Spec OpenAPI 3.0 |

**Formato de resposta padrão:**
```json
{ "success": true, "data": { ... }, "message": "..." }
{ "success": false, "message": "Mensagem de erro." }
```

---

## Pré-requisitos

- [Docker](https://www.docker.com/) e Docker Compose instalados
- Node.js 18+ e npm (para rodar o frontend localmente)

---

## Como rodar o projeto

### 1. Clone o repositório

```bash
git clone https://github.com/wesley-gomes-sje/VWTurismo.git
cd VWTurismo
```

### 2. Configure as variáveis de ambiente

```bash
cp backend/.env.example backend/.env
```

Edite `backend/.env`:

```env
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=vwturismo
MYSQL_USER=vwuser
MYSQL_PASSWORD=vwpass
MYSQL_DB_HOST=db
JWT_SECRET=sua-chave-secreta-com-no-minimo-32-caracteres
JWT_EXPIRATION=3600
```

### 3. Suba os containers

```bash
docker-compose up -d
```

### 4. Execute as migrations

```bash
docker exec vw_turismo php database/create_all_tables.php
```

Saída esperada:

```
Iniciando a criação de todas as tabelas...
Todas as tabelas foram criadas com sucesso.
Seed: usuário admin criado com sucesso.
  E-mail: admin@vwturismo.com
  Senha:  admin123
```

### 5. Inicie o frontend

```bash
cd frontend
npm install
npm run dev
```

### 6. Acesse a aplicação

| Interface | URL |
|-----------|-----|
| Frontend React | [http://localhost:3000](http://localhost:3000) |
| API REST | [http://localhost:8080/api](http://localhost:8080/api) |
| Swagger UI | [http://localhost:8080/api/docs](http://localhost:8080/api/docs) |

**Credenciais padrão do admin:**

| Campo | Valor |
|-------|-------|
| E-mail | `admin@vwturismo.com` |
| Senha | `admin123` |

---

## Testes

Os testes rodam dentro do container Docker ou localmente:

```bash
# Via Docker
docker exec vw_turismo ./vendor/bin/phpunit --no-coverage

# Local (PHP 8.3+ e Composer instalados)
cd backend
composer install
./vendor/bin/phpunit --no-coverage
```

Cobertura atual: **151 testes, 200+ assertions**.

Suites:
- `Database/` — conexão singleton
- `Model/` — City, User, Vehicle, Route, Ticket, Login
- `Router/` — dispatch, parâmetros dinâmicos
- `Middleware/` — AuthMiddleware, JwtMiddleware
- `Services/` — JwtService
- `Controller/Api/` — todos os controllers da API

---

## Comandos úteis

| Ação | Comando |
|------|---------|
| Subir containers | `docker-compose up -d` |
| Parar containers | `docker-compose down` |
| Entrar no container | `docker exec -it vw_turismo bash` |
| Logs da aplicação | `docker-compose logs -f app` |
| Logs do banco | `docker-compose logs -f db` |
| Rodar testes | `docker exec vw_turismo ./vendor/bin/phpunit --no-coverage` |
| Build do frontend | `cd frontend && npm run build` |
