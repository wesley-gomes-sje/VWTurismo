# CLAUDE.md — Guia de Contexto do Projeto VWTurismo

> Este arquivo serve como memória persistente para o assistente de IA.
> Leia-o integralmente no início de cada sessão antes de tomar qualquer ação.

---

## O que é o projeto

**VWTurismo** é uma aplicação web PHP MVC artesanal (sem framework) para uma empresa de turismo em ônibus. Possui dois perfis de usuário:

- **admin** — gerencia cidades, veículos, rotas e visualiza todas as passagens e clientes
- **user (cliente)** — compra passagens entre cidades e consulta suas próprias passagens

---

## Stack e dependências

| Item | Detalhe |
|------|---------|
| Linguagem | PHP 8.3 (sem framework) |
| Banco | MySQL via PDO |
| Autenticação atual | Session (`$_SESSION`) |
| `vlucas/phpdotenv` | Carrega variáveis do `.env` |
| `firebase/php-jwt` | Instalado, **ainda não usado** — decisão pendente (Etapa 8) |
| `phpunit/phpunit ^11` | Testes unitários (adicionado na Etapa 2) |

---

## Estrutura de diretórios

```
VWTurismo/
├── index.php                  ← Entry point: usa Router para despachar
├── Router.php                 ← class Router — dispatch por método HTTP + URI (Etapa 3)
├── connection.php             ← Classe Connection legacy (só usada por migration scripts)
├── auth.php                   ← Função global checkAuth() — carregada via composer files
├── logout.php                 ← Não mais usado diretamente; rota /logout no routes/web.php
├── routes/
│   └── web.php                ← Mapa explícito de todas as rotas (Etapa 3)
├── Controller/
│   ├── AuthController.php     ← Stub vazio (a implementar em Etapa 4)
│   ├── loginController.php
│   ├── userController.php
│   ├── cityController.php
│   ├── vehicleController.php
│   ├── routeController.php
│   └── ticketsController.php
├── Model/                     ← Todos aceitam ?PDO injetado
│   ├── loginModel.php         ← class Login
│   ├── User.php               ← class User
│   ├── cityModel.php          ← class City
│   ├── vehicleModel.php       ← class Vehicle
│   ├── routeModel.php         ← class Route
│   └── ticketModel.php        ← class Ticket
├── View/
│   ├── menuView.php           ← Todas as telas admin/cliente (a quebrar em Etapa 7)
│   ├── cadUsuarioView.php     ← Formulários de login e cadastro
│   └── Templates/
│       ├── templateAdm.php
│       ├── templateCustomer.php
│       └── templateUser.php
├── middlewares/
│   └── AuthMiddleware.php     ← Stub vazio (a implementar em Etapa 4)
├── helpers/
│   └── jwt_helper.php         ← Stub vazio (decisão em Etapa 8)
├── config/
│   └── config.php             ← Stub vazio
├── public/
│   ├── index.php              ← Bootstrap para quando web root = public/
│   └── .htaccess              ← (ver .htaccess na raiz)
├── .htaccess                  ← Redireciona tudo para index.php (usa REQUEST_URI)
├── tests/
│   ├── bootstrap.php          ← chdir + autoload + error_log=/dev/null
│   └── Unit/
│       └── RouterTest.php     ← 13 testes do Router (Etapa 3)
├── phpunit.xml                ← Configuração PHPUnit 11
├── database/
│   ├── create_all_tables.php
│   └── migrations/
│       ├── create_table_tickets.php ← price DECIMAL(10,2)
│       └── ...
├── context.md                 ← Diagnóstico completo do projeto
└── CLAUDE.md                  ← Este arquivo
```

---

## Banco de dados — tabelas

| Tabela | Colunas principais |
|--------|-------------------|
| `users` | id, name, email, password (bcrypt), profile (admin\|user), status, timestamps |
| `cities` | id, name, status (soft delete via UPDATE status=0), timestamps |
| `vehicles` | id, brand, model, plate, year |
| `routes` | id, origin (FK cities), destination (FK cities), distance |
| `tickets` | id, passenger (FK users), route (FK routes), vehicle (FK vehicles), price **DECIMAL(10,2)**, date, status, timestamps |

---

## Como o roteamento funciona (Etapa 3)

1. Toda requisição vai para `index.php` (ou `public/index.php` → `index.php`)
2. O `.htaccess` encaminha para `index.php` preservando `REQUEST_URI`
3. `index.php` cria um `Router` com fallback para `loginController::fillLogin()`
4. `routes/web.php` registra todas as rotas explicitamente via `$router->add()`
5. `$router->dispatch($method, $uri)` faz o match e chama `[Classe, 'método']`
6. Sem match → handler notFound exibe o formulário de login

### Router API

```php
$router = new Router(?callable $notFoundHandler = null);
$router->add(string $method, string $uri, callable|array $handler): void;
$router->dispatch(string $method, string $uri): bool;
```

- `dispatch()` retorna `true` se encontrou rota, `false` se não
- URI é normalizada: query string removida, método em uppercase
- Handler pode ser `[ClassName::class, 'method']` ou `callable`

---

## Padrão de injeção de dependência nos Models

Todos os models aceitam PDO opcional:

```php
public function __construct(?PDO $pdo = null)
{
    $this->pdo = $pdo ?? Connection::getInstance();
}
```

- **Produção**: `new City()` → usa `App\Database\Connection::getInstance()`
- **Testes**: `new City($mockPdo)` → usa o mock injetado

---

## Plano de refatoração — estado atual

| Etapa | Descrição | Status | Branch | PR |
|-------|-----------|--------|--------|----|
| 1 | Autoloading PSR-4 + Namespaces | ✅ Concluída | `refactor/etapa-1-autoloading-namespaces` | [#3](https://github.com/wesley-gomes-sje/VWTurismo/pull/3) |
| 2 | Conexão com o banco (DI / singleton) + TDD | ✅ Concluída | `refactor/etapa-2-database-connection` | [#4](https://github.com/wesley-gomes-sje/VWTurismo/pull/4) |
| 3 | Roteador simples com mapeamento explícito | ✅ Concluída | `refactor/etapa-3-router` | [#5](https://github.com/wesley-gomes-sje/VWTurismo/pull/5) |
| 4 | Autenticação e Middleware | ✅ Concluída | `refactor/etapa-4-auth-middleware` | [#6](https://github.com/wesley-gomes-sje/VWTurismo/pull/6) |
| 5 | Refatorar Models (responsabilidade única) | ✅ Concluída | `refactor/etapa-5-models` | [#7](https://github.com/wesley-gomes-sje/VWTurismo/pull/7) |
| 6 | Refatorar Controllers (extrair helpers) | ⏳ Pendente | — | — |
| 7 | Refatorar Views (templates reais, sem concatenação) | ⏳ Pendente | — | — |
| 8 | Limpeza final (JWT, CSRF, código morto) | ⏳ Pendente | — | — |

---

## Etapa 3 — Concluída (detalhes)

**Branch:** `refactor/etapa-3-router`
**Base:** `main` (commit `ad3791d`)

### O que foi feito

- Instalado PHPUnit 11, criado `phpunit.xml` e `tests/bootstrap.php`
- Criado `Router.php` com:
  - `add(method, uri, handler)` — registra rotas
  - `dispatch(method, uri)` — normaliza método/URI, faz match, chama handler
  - Handler injetável para 404 (testável sem mocks complexos)
- Criado `routes/web.php` com mapeamento explícito de todas as 20+ rotas
- Atualizado `index.php`: usa `Router`, sem `require_once` por controller, sem `new $classe()`
- Atualizado `public/index.php`: bootstrap limpo para web root em `public/`
- Atualizado `.htaccess`: encaminha para `index.php` usando `REQUEST_URI` (sem `?url=`)
- **13 testes unitários passando**, 19 assertions

### Commits da Etapa 3

```
build: install PHPUnit 11 and configure test infrastructure for Etapa 3
test:  add RouterTest with 13 unit tests (Red phase — TDD)
feat:  add Router class with explicit method+uri dispatching (Green phase)
feat:  add explicit route map in routes/web.php
refactor: replace dynamic class dispatch in index.php with Router
```

---

## Etapa 4 — Concluída (detalhes)

**Branch:** `refactor/etapa-4-auth-middleware`
**Base:** `main` (commit `ad3791d`)

### O que foi feito

- Criado `middlewares/AuthMiddleware.php` com:
  - `check(): bool` — puro, verifica `$_SESSION['idUser']` (testável sem side effects)
  - `handle(?callable $redirectFn): bool` — redireciona para `/login` se não autenticado; `$redirectFn` injetável em testes evita `header()`/`exit()`
  - `protect(handler, redirectFn): callable` — envolve um handler com verificação de auth; para execução se não autenticado
- Todas as rotas protegidas envolvidas com `AuthMiddleware::protect()` em `routes/web.php`
- `session_regenerate_id(true)` adicionado ao `loginModel::login()` após login bem-sucedido (previne session fixation)
- Removido `checkAuth()` do construtor do `ticketsController` — middleware centraliza a responsabilidade
- **9 testes unitários passando**, 10 assertions

### Commits da Etapa 4

```
build: install PHPUnit 11 and configure test infrastructure for Etapa 4
test:  add AuthMiddlewareTest with 9 unit tests (Red phase — TDD)
feat:  add AuthMiddleware with check(), handle() and protect() (Green phase)
feat:  apply AuthMiddleware::protect() to all protected routes in routes/web.php
fix:   add session_regenerate_id on login and remove checkAuth() from controllers
```

---

## Etapa 5 — Concluída (detalhes)

**Branch:** `refactor/etapa-5-models`
**Base:** `main` (commit `ad3791d`)

### O que foi feito

- Criado `Database/Connection.php` (`App\Database\Connection`) com `getInstance()`, `setInstance()`, `reset()`
- Todos os 6 models refatorados:
  - `?PDO $pdo = null` no construtor; fallback para `Connection::getInstance()`
  - Removido `require_once './connection.php'` de todos
  - Substituído `echo`/`print_r` por `error_log()`
- `Login::login()` agora **retorna `array` com dados do usuário** (ou `false`) — sem mais `$_SESSION`
- `loginController::login()` assume gerenciamento de sessão + `session_regenerate_id(true)`
- Corrigido bug SQL em `User`: `WHERE profile=user` → `WHERE profile='user'`
- **54 testes unitários passando**, 78 assertions

### Commits da Etapa 5

```
build:   install PHPUnit 11, configure test infra and add App\Database\Connection singleton
test:    add 54 unit tests for all 6 models (Red phase — TDD)
refactor: inject PDO into all models and remove session logic from Login
refactor: move session management from loginModel to loginController
```

---

## Etapa 6 — Próxima (planejamento)

**Objetivo:** Refatorar Controllers — extrair helpers duplicados.

**O que será feito:**
- Extrair `sanitizeString()` duplicado em todos os controllers para um `Helpers/Sanitizer.php` ou trait
- Remover `require_once` de views e models dos controllers (autoloader cuida disso)
- Remover `session_start()` do `loginController` (já feito no `index.php`)
- Garantir que todos os controllers recebem dependências via construtor (injeção)

---

## Convenções adotadas no projeto

- **Branch por etapa:** `refactor/etapa-N-descricao-curta` criada a partir de `main`
- **Commits semânticos:** `build:`, `refactor:`, `feat:`, `fix:`, `docs:`, `chore:`, `test:`
- **`main` nunca quebra** — todo trabalho em branch separada
- **TDD obrigatório** a partir da Etapa 2 — escrever testes antes de implementar
- **Sem over-engineering** — soluções simples e diretas
- **Português** nos textos de UI e mensagens de erro do domínio

---

## Arquivos importantes para leitura rápida

| Arquivo | Por que ler |
|---------|-------------|
| `context.md` | Diagnóstico completo com todos os problemas identificados |
| `Router.php` | Implementação do roteador simples |
| `routes/web.php` | Mapa de todas as rotas da aplicação |
| `index.php` | Entry point atualizado (Etapa 3) |
| `tests/Unit/RouterTest.php` | Exemplo de testes do Router |
