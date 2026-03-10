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
├── index.php                  ← Router principal (entry point quando web root = /)
├── connection.php             ← Classe Connection legacy (só usada por migration scripts)
├── auth.php                   ← Função global checkAuth() — carregada via composer files
├── logout.php                 ← Destroi sessão e redireciona para /login
├── Database/
│   └── Connection.php         ← App\Database\Connection — singleton PDO (Etapa 2)
├── Controller/                ← namespace App\Controller (Etapa 1)
│   ├── AuthController.php     ← Stub vazio (a implementar)
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
├── routes/
│   └── web.php                ← Stub vazio (a implementar em Etapa 3)
├── public/
│   ├── index.php              ← Bootstrap: chdir + require ../index.php
│   └── .htaccess              ← Redireciona tudo para index.php?url=$1
├── tests/
│   ├── bootstrap.php          ← chdir + autoload + error_log=/dev/null
│   └── Unit/
│       ├── Database/ConnectionTest.php
│       └── Model/{City,User,Vehicle,Route,Ticket,Login}Test.php
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

## Como o roteamento funciona (estado atual)

1. Toda requisição vai para `index.php` (ou `public/index.php` → `index.php`)
2. O `.htaccess` passa a URL como `?url=controller/metodo`
3. `index.php` faz `explode('/', $url)` e monta `App\Controller\{nome}Controller`
4. Instancia a classe e chama o método dinamicamente
5. Se não existir, cai em `loginController::fillLogin()`

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

| Etapa | Descrição | Status | Branch |
|-------|-----------|--------|--------|
| 1 | Autoloading PSR-4 + Namespaces | ✅ Concluída | `refactor/etapa-1-autoloading-namespaces` |
| 2 | Conexão com o banco (DI / singleton) + TDD | ✅ Concluída | `refactor/etapa-2-database-connection` |
| 3 | Roteador simples com mapeamento explícito | ⏳ Pendente | — |
| 4 | Autenticação e Middleware | ⏳ Pendente | — |
| 5 | Refatorar Models (responsabilidade única) | ⏳ Pendente | — |
| 6 | Refatorar Controllers (extrair helpers) | ⏳ Pendente | — |
| 7 | Refatorar Views (templates reais, sem concatenação) | ⏳ Pendente | — |
| 8 | Limpeza final (JWT, CSRF, código morto) | ⏳ Pendente | — |

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
| `composer.json` | PSR-4 da Etapa 1 (`App\Controller`, `App\Model`, etc.) vs PSR-4 da Etapa 2 (`App\Database`) + `require-dev` PHPUnit | Unificado: PSR-4 para Controller, View, Middleware, Database; **`Model/` movido para `classmap`** (filenames `cityModel.php` não batem com PSR-4 que exige `City.php`) |
| `CLAUDE.md` | Dois arquivos criados independentemente em cada branch | Conteúdo unificado: estrutura da Etapa 1 + informações da Etapa 2 adicionadas |
| `database/Connection.php` | Arquivo não-rastreado no working tree bloqueava o merge | Removido (era artefato de sessão anterior; o merge trouxe `Database/Connection.php` corretamente) |
| `tests/Unit/Model/*.php` | Testes da Etapa 2 usavam `use City;` (sem namespace) | Atualizado para `use App\Model\City;` etc., pois os models agora têm `namespace App\Model` |

### Critério de resolução

> Em todos os conflitos de models: manteve-se o **namespace `App\Model`** introduzido pela Etapa 1 e o **construtor com injeção de dependência** (`?PDO $pdo = null`) introduzido pela Etapa 2. Os conflitos de formatação SQL (apenas whitespace) foram resolvidos mantendo a versão do HEAD (Etapa 1), que é mais legível.
>
> **Descoberta importante:** `Model/` não pode usar PSR-4 pois os arquivos se chamam `cityModel.php`, `ticketModel.php`, etc. — o PSR-4 exigiria `City.php`, `Ticket.php`. Mantido como `classmap`, que escaneia o namespace declarado no arquivo independente do nome do arquivo.

---

## Etapa 3 — Próxima (planejamento)

**Objetivo:** Roteador explícito — eliminar `new $classe()` dinâmico.

**O que será feito:**
- `routes/web.php`: mapeamento explícito `'GET /city/open' => [cityController::class, 'open']`
- `Router.php`: despacha com base em método HTTP + URI
- Eliminar `explode('/', $url)` e `new $classe()` dinâmico do `index.php`
- Criar branch a partir de `esteira1-ai` (não de `main`)
- TDD: escrever testes para o Router antes de implementar

---

## Convenções adotadas no projeto

- **Branch por etapa:** `refactor/etapa-N-descricao-curta` criada a partir de `main` (branches isoladas) ou mergeada sequencialmente na `esteira1-ai`
- **Commits semânticos:** `build:`, `refactor:`, `feat:`, `fix:`, `docs:`, `test:`
- **`main` nunca quebra** — todo trabalho em branch separada
- **TDD obrigatório** a partir da Etapa 2 — escrever testes antes de implementar
- **Sem over-engineering** — soluções simples e diretas
- **Português** nos textos de UI e mensagens de erro do domínio

---

## Arquivos importantes para leitura rápida

| Arquivo | Por que ler |
|---------|-------------|
| `context.md` | Diagnóstico completo com todos os problemas identificados |
| `index.php` | Entender o roteamento atual antes de modificar qualquer fluxo |
| `Database/Connection.php` | Nova classe de conexão singleton |
| `tests/Unit/Model/CityTest.php` | Exemplo de como escrever testes para models |
| `View/menuView.php` | Entender a estrutura das views antes da Etapa 7 |
