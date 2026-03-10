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
├── Database/
│   └── Connection.php         ← App\Database\Connection — singleton PDO (Etapa 2)
├── Controller/                ← namespace App\Controller (Etapa 1)
│   ├── AuthController.php     ← Stub vazio (a implementar em Etapa 4)
│   ├── loginController.php
│   ├── userController.php
│   ├── cityController.php
│   ├── vehicleController.php
│   ├── routeController.php
│   └── ticketsController.php
├── Model/                     ← namespace App\Model (Etapa 1) + ?PDO injetado (Etapa 2)
│   ├── loginModel.php         ← class Login
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
│       ├── Database/ConnectionTest.php
│       ├── Model/{City,User,Vehicle,Route,Ticket,Login}Test.php
│       └── RouterTest.php     ← 13 testes do Router (Etapa 3)
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

## Padrão de injeção de dependência nos Models (Etapa 2)

Todos os models agora aceitam PDO opcional:

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
| 4 | Autenticação e Middleware | ⏳ Pendente | — | — |
| 5 | Refatorar Models (responsabilidade única) | ⏳ Pendente | — | — |
| 6 | Refatorar Controllers (extrair helpers) | ⏳ Pendente | — | — |
| 7 | Refatorar Views (templates reais, sem concatenação) | ⏳ Pendente | — | — |
| 8 | Limpeza final (JWT, CSRF, código morto) | ⏳ Pendente | — | — |

---

## Etapa 1 — Concluída (detalhes)

**Branch:** `refactor/etapa-1-autoloading-namespaces`
**Base:** `main` (commit `ad3791d`)

### O que foi feito

- `composer.json`: adicionado `autoload.psr-4` mapeando `App\Controller`, `App\Model`, `App\View`, `App\Middleware`; `connection.php` via `classmap`; `auth.php` via `files`
- Todos os **Models** receberam `namespace App\Model` + `use Connection` + remoção de `require_once`
- Todas as **Views** receberam `namespace App\View` + remoção de `require_once './auth.php'`
- Todos os **Controllers** receberam `namespace App\Controller` + `use` statements das dependências
- `index.php`: removidos todos os `require_once` de controllers/views; adicionado `require vendor/autoload.php`; `session_start()` movido para cá; resolução de classe usa namespace completo

### Bugs corrigidos nesta etapa

- `print_r(errorInfo())` e `echo $e->getMessage()` em todos os Models → `error_log()`
- `showCustomers()` em `User`: `WHERE profile=user` → `WHERE profile='user'`
- `userController::$userView` apontava para `menuView` → corrigido para `cadUsuarioView`

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
- Criado `Database/Connection.php` (`App\Database\Connection`) como singleton com:
  - `getInstance()` — retorna o PDO compartilhado
  - `setInstance(PDO)` — injeta instância para testes
  - `reset()` — limpa instância entre testes
- Todos os 6 Models atualizados para aceitar `?PDO $pdo = null` no construtor
- **64 testes unitários passando**, 65 assertions, cobertura dos 6 models + Connection
- Corrigido bug: `price` na migration `INT` → `DECIMAL(10,2)`

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

## Merge: esteira1-ai ← refactor/etapa-2-database-connection

### Conflitos resolvidos

| Arquivo | Conflito | Resolução |
|---------|----------|-----------|
| `Model/cityModel.php` | Namespace `App\Model` (Etapa 1) vs `use App\Database\Connection` (Etapa 2); construtor antigo vs DI | Mantido `namespace App\Model` + `use App\Database\Connection` + construtor `?PDO $pdo = null` |
| `Model/loginModel.php` | Mesmo padrão de namespace + construtor | Idem |
| `Model/User.php` | Namespace + construtor + formatação SQL (`SELECT id,email` vs `SELECT id, email`) | Namespace + DI; SQL formatado com espaços |
| `Model/vehicleModel.php` | Namespace + construtor + whitespace em `all()` | Namespace + DI; formatação unificada |
| `Model/routeModel.php` | Namespace + construtor + formatação SQL do JOIN em `all()` | Namespace + DI; SQL da Etapa 1 mantido |
| `Model/ticketModel.php` | Namespace + construtor + quebra de linha em SQL de `show()`, `all()`, `showTicketsByPassenger()` | Namespace + DI; SQL da Etapa 1 mantido |
| `composer.json` | PSR-4 da Etapa 1 (`App\Controller`, `App\Model`, etc.) vs PSR-4 da Etapa 2 (`App\Database`) + `require-dev` PHPUnit | Unificado: todos os namespaces PSR-4 + `App\Database` + `require-dev` |
| `CLAUDE.md` | Dois arquivos criados independentemente em cada branch | Conteúdo unificado: estrutura da Etapa 1 + informações da Etapa 2 adicionadas |
| `database/Connection.php` | Arquivo não-rastreado no working tree bloqueava o merge | Removido (era artefato de sessão anterior; o merge trouxe `Database/Connection.php` corretamente) |
| `tests/Unit/Model/*.php` | Testes da Etapa 2 usavam `use City;` (sem namespace) | Atualizado para `use App\Model\City;` etc. |

### Critério de resolução

> Em todos os conflitos de models: manteve-se o **namespace `App\Model`** introduzido pela Etapa 1 e o **construtor com injeção de dependência** (`?PDO $pdo = null`) introduzido pela Etapa 2. Os conflitos de formatação SQL (apenas whitespace) foram resolvidos mantendo a versão do HEAD (Etapa 1), que é mais legível.
>
> **Descoberta importante:** `Model/` não pode usar PSR-4 pois os arquivos se chamam `cityModel.php`, `ticketModel.php`, etc. — o PSR-4 exigiria `City.php`, `Ticket.php`. Mantido como `classmap`, que escaneia o namespace declarado no arquivo independente do nome do arquivo.

---

## Merge: esteira1-ai ← refactor/etapa-3-router

### Conflitos resolvidos

| Arquivo | Conflito | Resolução |
|---------|----------|-----------|
| `composer.json` | PSR-4 do HEAD vs `Router.php` adicionado ao classmap na Etapa 3 | Mantido PSR-4 do HEAD + `Router.php` no classmap |
| `index.php` | Roteamento dinâmico (HEAD) vs Router dispatch (Etapa 3) | Etapa 3: `Router` com FQN `App\Controller\loginController` no notFoundHandler |
| `public/index.php` | Dois bootstraps ligeiramente diferentes | Etapa 3: docblock mais limpo |
| `phpunit.xml` | Cobertura apenas `Database/`+`Model/` (HEAD) vs `Database/`+`Model/`+`Router.php` (Etapa 3) | Unificado: todas as três fontes |
| `CLAUDE.md` | HEAD com Etapa 1+2; Etapa 3 com planejamento atualizado | Unificado: todo o histórico + detalhes da Etapa 3 |
| `routes/web.php` | Auto-merged com nomes curtos de classe (`['loginController', 'fillLogin']`) | Corrigido para FQN (`['App\Controller\loginController', 'fillLogin']`) pois controllers têm namespace em esteira1-ai |

### Critério de resolução

> Priorizou-se sempre o conteúdo mais recente e completo. Em `routes/web.php`, o auto-merge trouxe nomes curtos de classe que não funcionariam com os controllers namespaceados — todos foram atualizados para FQN `App\Controller\*`.

---

## Etapa 4 — Próxima (planejamento)

**Objetivo:** Autenticação centralizada via Middleware.

**O que será feito:**
- Implementar `middlewares/AuthMiddleware.php`:
  - `handle()` verifica `$_SESSION['idUser']` e redireciona para `/login` se não autenticado
  - Lógica extraída dos constructors dos controllers (atualmente `checkAuth()` de `auth.php`)
- Aplicar middleware nas rotas protegidas em `routes/web.php` ou via wrapper no Router
- `session_regenerate_id(true)` no login bem-sucedido (segurança)
- Remover `checkAuth()` dos constructors dos controllers
- TDD: testar que rotas protegidas redirecionam sem sessão, e passam com sessão válida

---

## Convenções adotadas no projeto

- **Branch por etapa:** `refactor/etapa-N-descricao-curta` criada a partir de `main` (branches isoladas) ou mergeada sequencialmente na `esteira1-ai`
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
| `Database/Connection.php` | Nova classe de conexão singleton |
| `tests/Unit/RouterTest.php` | Exemplo de testes do Router |
| `tests/Unit/Model/CityTest.php` | Exemplo de como escrever testes para models |
| `View/menuView.php` | Entender a estrutura das views antes da Etapa 7 |
