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
| `firebase/php-jwt` | **Removido na Etapa 8** — não utilizado; autenticação permanece Session |
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
│   └── web.php                ← Mapa explícito de todas as rotas (Etapa 3+4)
├── Database/
│   └── Connection.php         ← App\Database\Connection — singleton PDO (Etapa 2)
├── Controller/                ← namespace App\Controller (Etapa 1)
│   ├── AuthController.php     ← Stub vazio
│   ├── loginController.php    ← Gerencia sessão após login (Etapa 5)
│   ├── userController.php
│   ├── cityController.php
│   ├── vehicleController.php
│   ├── routeController.php
│   └── ticketsController.php
├── Model/                     ← namespace App\Model (Etapa 1) + ?PDO injetado (Etapa 2)
│   ├── loginModel.php         ← class Login — retorna array|false, sem $_SESSION (Etapa 5)
│   ├── User.php               ← class User
│   ├── cityModel.php          ← class City
│   ├── vehicleModel.php       ← class Vehicle
│   ├── routeModel.php         ← class Route
│   └── ticketModel.php        ← class Ticket
├── View/                      ← namespace App\View (Etapa 1)
│   ├── menuView.php           ← Todas as telas admin/cliente (a quebrar em Etapa 7)
│   ├── cadUsuarioView.php     ← Formulários de login e cadastro
│   └── Templates/
│       ├── templateAdm.php
│       ├── templateCustomer.php
│       └── templateUser.php
├── middlewares/               ← namespace App\Middleware
│   └── AuthMiddleware.php     ← check(), handle(), protect() — implementado (Etapa 4)
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
│       ├── Database/ConnectionTest.php
│       ├── Model/{City,User,Vehicle,Route,Ticket,Login}Test.php
│       ├── RouterTest.php         ← 13 testes do Router (Etapa 3)
│       └── AuthMiddlewareTest.php ← 9 testes do Middleware (Etapa 4)
├── phpunit.xml                ← Configuração PHPUnit 11
├── database/
│   ├── create_all_tables.php
│   └── migrations/
│       ├── create_table_tickets.php ← price agora DECIMAL(10,2)
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

## Como o roteamento funciona (Etapa 3+4)

1. Toda requisição vai para `index.php` (ou `public/index.php` → `index.php`)
2. O `.htaccess` encaminha para `index.php` preservando `REQUEST_URI`
3. `index.php` cria um `Router` com fallback para `loginController::fillLogin()`
4. `routes/web.php` registra todas as rotas via `$router->add()`
5. Rotas protegidas são envolvidas com `AuthMiddleware::protect()` (Etapa 4)
6. `$router->dispatch($method, $uri)` faz o match e chama o handler

### Router API

```php
$router = new Router(?callable $notFoundHandler = null);
$router->add(string $method, string $uri, callable|array $handler): void;
$router->dispatch(string $method, string $uri): bool;
```

### AuthMiddleware API

```php
AuthMiddleware::check(): bool
AuthMiddleware::handle(?callable $redirectFn = null): bool
AuthMiddleware::protect(callable|array $handler, ?callable $redirectFn = null): callable
```

---

## Padrão de injeção de dependência nos Models (Etapa 2+5)

Todos os models aceitam PDO opcional:

```php
public function __construct(?PDO $pdo = null)
{
    $this->pdo = $pdo ?? Connection::getInstance();
}
```

- **Produção**: `new City()` → usa `App\Database\Connection::getInstance()`
- **Testes**: `new City($mockPdo)` → usa o mock injetado

`Login::login()` retorna `array|false` — sem `$_SESSION`. O controller gerencia a sessão.

---

## Plano de refatoração — estado atual

| Etapa | Descrição | Status | Branch | PR |
|-------|-----------|--------|--------|----|
| 1 | Autoloading PSR-4 + Namespaces | ✅ Concluída | `refactor/etapa-1-autoloading-namespaces` | [#3](https://github.com/wesley-gomes-sje/VWTurismo/pull/3) |
| 2 | Conexão com o banco (DI / singleton) + TDD | ✅ Concluída | `refactor/etapa-2-database-connection` | [#4](https://github.com/wesley-gomes-sje/VWTurismo/pull/4) |
| 3 | Roteador simples com mapeamento explícito | ✅ Concluída | `refactor/etapa-3-router` | [#5](https://github.com/wesley-gomes-sje/VWTurismo/pull/5) |
| 4 | Autenticação e Middleware | ✅ Concluída | `refactor/etapa-4-auth-middleware` | [#6](https://github.com/wesley-gomes-sje/VWTurismo/pull/6) |
| 5 | Refatorar Models (responsabilidade única) | ✅ Concluída | `refactor/etapa-5-models` | [#7](https://github.com/wesley-gomes-sje/VWTurismo/pull/7) |
| 6 | Refatorar Controllers (extrair helpers) | ✅ Concluída | `refactor/etapa-6-controllers` | — |
| 7 | Refatorar Views (templates reais, sem concatenação) | ✅ Concluída | `refactor/etapa-7-views` | — |
| 8 | Limpeza final (JWT removido, stubs deletados) | ✅ Concluída | `refactor/etapa-8-cleanup` | — |

---

## Etapa 1 — Concluída (detalhes)

**Branch:** `refactor/etapa-1-autoloading-namespaces`
**Base:** `main` (commit `ad3791d`)

### O que foi feito

- `composer.json`: adicionado `autoload.psr-4` mapeando `App\Controller`, `App\Model`, `App\View`, `App\Middleware`; `connection.php` via `classmap`; `auth.php` via `files`
- Todos os **Models** receberam `namespace App\Model` + `use Connection` + remoção de `require_once`
- Todas as **Views** receberam `namespace App\View` + remoção de `require_once './auth.php'`
- Todos os **Controllers** receberam `namespace App\Controller` + `use` statements das dependências
- `index.php`: removidos todos os `require_once` de controllers/views; adicionado `require vendor/autoload.php`; `session_start()` movido para cá

### Commits

```
afb9dea docs: add CLAUDE.md with project context and refactoring roadmap
280c7dc refactor: update router and bootstrap to use autoloader
24fe611 refactor: add App\Controller namespace to all Controller classes
2081bc3 refactor: add App\View namespace to all View classes
33c722a refactor: add App\Model namespace to all Model classes
edf2a74 build: add PSR-4 autoloading and files/classmap to composer.json
```

---

## Etapa 2 — Concluída (detalhes)

**Branch:** `refactor/etapa-2-database-connection`
**Base:** `main` (commit `ad3791d`)

### O que foi feito

- Instalado `phpunit/phpunit ^11.0` como dev dependency
- Criado `phpunit.xml` com suite Unit e configuração de source coverage
- Criado `tests/bootstrap.php` com `chdir()` + `ini_set('error_log', '/dev/null')`
- Criado `Database/Connection.php` (`App\Database\Connection`) como singleton
- Todos os 6 Models atualizados para aceitar `?PDO $pdo = null` no construtor
- **64 testes unitários passando**, 65 assertions

### Commits

```
9acd32d docs: update CLAUDE.md with Etapa 2 status and TDD conventions
54e4153 fix: change tickets.price column type from INT to DECIMAL(10,2)
43d1ac5 refactor: inject PDO into Models via optional constructor parameter
60efef3 feat: add App\Database\Connection singleton
84b178b build: install PHPUnit 11 and configure test infrastructure
```

---

## Etapa 3 — Concluída (detalhes)

**Branch:** `refactor/etapa-3-router`
**Base:** `main` (commit `ad3791d`)

### O que foi feito

- Criado `Router.php` com `add()` e `dispatch()`
- Criado `routes/web.php` com mapeamento explícito de todas as rotas
- Atualizado `index.php`: usa `Router`, sem `new $classe()` dinâmico
- Atualizado `.htaccess`: usa `REQUEST_URI` (sem `?url=`)
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

- Criado `middlewares/AuthMiddleware.php` com `check()`, `handle()`, `protect()`
- Todas as rotas protegidas envolvidas com `AuthMiddleware::protect()` em `routes/web.php`
- `session_regenerate_id(true)` no login bem-sucedido (previne session fixation)
- Removido `checkAuth()` dos constructors dos controllers
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

- Todos os 6 models refatorados:
  - `?PDO $pdo = null` no construtor; fallback para `Connection::getInstance()`
  - Removido `require_once './connection.php'` de todos
  - Substituído `echo`/`print_r` por `error_log()`
  - Código simplificado (ternários, mensagens de erro prefixadas com classe)
- `Login::login()` agora **retorna `array` com dados do usuário** (ou `false`) — sem `$_SESSION`
- `loginController::login()` assume gerenciamento de sessão + `session_regenerate_id(true)`
- Corrigido bug SQL em `User`: `WHERE profile=user` → `WHERE profile='user'`
- `calculatePrice()` com tipos explícitos: `float $distance): float`
- **54 testes unitários passando**, 78 assertions

### Commits da Etapa 5

```
build:    install PHPUnit 11, configure test infra and add App\Database\Connection singleton
test:     add 54 unit tests for all 6 models (Red phase — TDD)
refactor: inject PDO into all models and remove session logic from Login
refactor: move session management from loginModel to loginController
```

---

## Merge: esteira1-ai ← refactor/etapa-2-database-connection

### Conflitos resolvidos

| Arquivo | Conflito | Resolução |
|---------|----------|-----------|
| `Model/*.php` | Namespace `App\Model` (Etapa 1) vs `use App\Database\Connection` + DI (Etapa 2) | Mantido namespace + DI |
| `composer.json` | PSR-4 vs PSR-4 + `App\Database` + `require-dev` | Unificado |
| `CLAUDE.md` | Dois arquivos criados independentemente | Conteúdo unificado |
| `database/Connection.php` | Arquivo não-rastreado bloqueava o merge | Removido |
| `tests/Unit/Model/*.php` | `use City;` sem namespace | Atualizado para `use App\Model\City;` etc. |

---

## Merge: esteira1-ai ← refactor/etapa-3-router

### Conflitos resolvidos

| Arquivo | Conflito | Resolução |
|---------|----------|-----------|
| `composer.json` | PSR-4 do HEAD vs `Router.php` no classmap da Etapa 3 | PSR-4 + `Router.php` no classmap |
| `index.php` | Roteamento dinâmico vs Router dispatch | Etapa 3 com FQN |
| `phpunit.xml` | Database+Model vs + Router.php | Unificado |
| `routes/web.php` | Auto-merged com nomes curtos | Corrigido para FQN `App\Controller\*` |

---

## Merge: esteira1-ai ← refactor/etapa-4-auth-middleware

### Conflitos resolvidos

| Arquivo | Conflito | Resolução |
|---------|----------|-----------|
| `composer.json` | PSR-4 do HEAD vs classmap-only da Etapa 4 | Mantido PSR-4 do HEAD |
| `index.php` | FQN (HEAD) vs nome curto (Etapa 4) | Mantido FQN do HEAD |
| `phpunit.xml` | Database+Model+Router vs só middlewares | Unificado: todas as quatro fontes |
| `Controller/ticketsController.php` | `checkAuth()` no construtor | Removido (middleware centraliza) |
| `Model/loginModel.php` | Sem `session_regenerate_id` vs com | Mantido `session_regenerate_id(true)` |
| `routes/web.php` | FQN sem proteção vs nomes curtos + protect | Combinado: FQN + `AuthMiddleware::protect()` |
| `middlewares/AuthMiddleware.php` | Sem namespace | Adicionado `namespace App\Middleware` |
| `AuthMiddlewareTest.php` | `use AuthMiddleware` sem namespace | Corrigido para `use App\Middleware\AuthMiddleware` |

---

## Merge: esteira1-ai ← refactor/etapa-5-models

### Conflitos resolvidos

| Arquivo | Conflito | Resolução |
|---------|----------|-----------|
| `Model/*.php` (6 arquivos) | HEAD com `namespace App\Model` + verbose; Etapa 5 sem namespace + compacto | Namespace do HEAD + código compacto da Etapa 5 |
| `Model/loginModel.php` | HEAD com `$_SESSION` + `session_regenerate_id`; Etapa 5 retorna array | Etapa 5: retorna `array\|false`, sem sessão |
| `Controller/loginController.php` | HEAD gerencia sessão via model; Etapa 5 usa variável `$user` indefinida | Corrigido: `$user = login(getEmail, getPassword)` + sessão no controller |
| `composer.json` | HEAD com PSR-4 completo + Router.php; Etapa 5 sem Controller/View/Middleware no PSR-4 | Mantido PSR-4 completo do HEAD |
| `phpunit.xml` | HEAD com Database+Model+middlewares+Router; Etapa 5 só Database+Model | Mantido completo do HEAD |
| `tests/bootstrap.php` | HEAD com docblock; Etapa 5 sem | Mantido docblock do HEAD |
| `tests/Unit/Model/*.php` (6 arquivos) | HEAD com `use App\Model\X`; Etapa 5 com `use X` | Etapa 5 (compacto) + FQN correto do HEAD |

### Critério de resolução

> Para os Models: namespace `App\Model` + `use PDO` + `use PDOException` do HEAD (necessário para PSR-4 e type hints) + implementação compacta da Etapa 5 (ternários, mensagens prefixadas).
> Para `loginController`: removida a dupla chamada a `login()` que a branch da Etapa 5 introduziria — usado `$user = login(getEmail(), getPassword())` corretamente.
> Para os Testes: versão compacta da Etapa 5 com `use App\Model\X` correto.

---

## Etapa 6 — Próxima (planejamento)

**Objetivo:** Refatorar Controllers — extrair helpers duplicados.

**O que será feito:**
- Extrair `sanitizeString()` duplicado em todos os controllers para um helper ou trait
- Remover `session_start()` do `loginController` (já feito no `index.php`)
- Garantir que todos os controllers recebem dependências via construtor

---

## Convenções adotadas no projeto

- **Branch por etapa:** `refactor/etapa-N-descricao-curta` criada a partir de `main` (branches isoladas) mergeadas sequencialmente na `esteira1-ai`
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
| `Database/Connection.php` | Classe de conexão singleton |
| `middlewares/AuthMiddleware.php` | Middleware de autenticação (Etapa 4) |
| `Model/loginModel.php` | Login retorna array (Etapa 5) |
| `Controller/loginController.php` | Gerencia sessão após login (Etapa 5) |
| `tests/Unit/Model/CityTest.php` | Exemplo de testes de models |
| `View/menuView.php` | Entender a estrutura das views antes da Etapa 7 |
