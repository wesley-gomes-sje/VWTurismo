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
| Linguagem | PHP (sem framework) |
| Banco | MySQL via PDO |
| Autenticação atual | Session (`$_SESSION`) |
| `vlucas/phpdotenv` | Carrega variáveis do `.env` |
| `firebase/php-jwt` | Instalado, **ainda não usado** — decisão pendente (Etapa 8) |

---

## Estrutura de diretórios

```
VWTurismo/
├── index.php                  ← Router principal (entry point quando web root = /)
├── connection.php             ← Classe Connection (PDO + phpdotenv), sem namespace
├── auth.php                   ← Função global checkAuth() — carregada via composer files
├── logout.php                 ← Destroi sessão e redireciona para /login
├── Controller/                ← namespace App\Controller
│   ├── AuthController.php     ← Stub vazio (a implementar)
│   ├── loginController.php
│   ├── userController.php
│   ├── cityController.php
│   ├── vehicleController.php
│   ├── routeController.php
│   └── ticketsController.php
├── Model/                     ← namespace App\Model
│   ├── loginModel.php         ← class Login
│   ├── User.php               ← class User
│   ├── cityModel.php          ← class City
│   ├── vehicleModel.php       ← class Vehicle
│   ├── routeModel.php         ← class Route
│   └── ticketModel.php        ← class Ticket
├── View/                      ← namespace App\View
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
│   └── config.php             ← Stub vazio (a usar em Etapa 2)
├── routes/
│   └── web.php                ← Stub vazio (a implementar em Etapa 3)
├── public/
│   ├── index.php              ← Bootstrap: chdir + require ../index.php
│   ├── .htaccess              ← Redireciona tudo para index.php?url=$1
│   └── assets/
│       ├── css/styles.css
│       └── js/main.js
├── database/
│   ├── create_all_tables.php  ← Script manual de migração
│   └── migrations/
│       ├── create_table_users.php
│       ├── create_table_cities.php
│       ├── create_table_vehicles.php
│       ├── create_table_routes.php
│       └── create_table_tickets.php
├── context.md                 ← Diagnóstico completo do projeto (leitura recomendada)
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
| `tickets` | id, passenger (FK users), route (FK routes), vehicle (FK vehicles), price, date, status, timestamps |

**Nota:** `price` está como `INT` no banco mas o cálculo (`distance * 0.5`) retorna float. Corrigir para `DECIMAL(10,2)` na Etapa 2.

---

## Como o roteamento funciona (estado atual)

1. Toda requisição vai para `index.php` (ou `public/index.php` → `index.php`)
2. O `.htaccess` passa a URL como `?url=controller/metodo`
3. `index.php` faz `explode('/', $url)` e monta `App\Controller\{nome}Controller`
4. Instancia a classe e chama o método dinamicamente
5. Se não existir, cai em `loginController::fillLogin()`

---

## Plano de refatoração — estado atual

| Etapa | Descrição | Status | Branch |
|-------|-----------|--------|--------|
| 1 | Autoloading PSR-4 + Namespaces | ✅ Concluída | `refactor/etapa-1-autoloading-namespaces` |
| 2 | Conexão com o banco (DI / singleton) | ⏳ Pendente | — |
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

- `composer.json`: adicionado `autoload.psr-4` mapeando os 4 namespaces; `connection.php` via `classmap`; `auth.php` via `files`
- Todos os **Models** receberam `namespace App\Model` + `use Connection` + remoção de `require_once`
- Todas as **Views** receberam `namespace App\View` + remoção de `require_once './auth.php'` (carregada pelo autoloader)
- Todos os **Controllers** receberam `namespace App\Controller` + `use` statements das dependências
- `index.php`: removidos todos os `require_once` de controllers/views; adicionado `require vendor/autoload.php`; `session_start()` movido para cá; resolução de classe usa namespace completo (`App\Controller\{X}Controller`)
- `public/index.php`: implementado como bootstrap real com `chdir(dirname(__DIR__))` + `require index.php`

### Bugs corrigidos nesta etapa

- `print_r(errorInfo())` e `echo $e->getMessage()` em todos os Models → substituídos por `error_log()`
- `showCustomers()` em `User`: SQL sem aspas em `WHERE profile=user` → corrigido para `profile='user'`
- `userController::$userView` apontava para `menuView` → corrigido para `cadUsuarioView`
- Outputs de dados do banco sem escape → adicionado `htmlspecialchars()` em toda a `menuView`

### Commits da Etapa 1

```
280c7dc refactor: update router and bootstrap to use autoloader
24fe611 refactor: add App\Controller namespace to all Controller classes
2081bc3 refactor: add App\View namespace to all View classes
33c722a refactor: add App\Model namespace to all Model classes
edf2a74 build: add PSR-4 autoloading and files/classmap to composer.json
```

---

## Etapa 2 — Próxima (planejamento)

**Objetivo:** Uma única conexão PDO reutilizada por toda a requisição; eliminar `new Connection()` dentro de cada Model.

**O que será feito:**
- Criar `src/Database/Connection.php` (ou mover `connection.php`) como singleton ou serviço simples
- Adicionar namespace `App\Database\Connection`
- Passar o PDO via construtor para os Models (injeção de dependência simples)
- Corrigir o tipo da coluna `price` de `INT` para `DECIMAL(10,2)` na migration
- Criar branch `refactor/etapa-2-database-connection`

---

## Convenções adotadas no projeto

- **Branch por etapa:** `refactor/etapa-N-descricao-curta` criada a partir de `main`
- **Commits semânticos:** `build:`, `refactor:`, `feat:`, `fix:`, `chore:`
- **`main` nunca quebra** — todo trabalho em branch separada
- **Sem over-engineering** — soluções simples e diretas, sem abstrações desnecessárias
- **Português** nos textos de UI e mensagens de erro do domínio

---

## Arquivos importantes para leitura rápida

| Arquivo | Por que ler |
|---------|-------------|
| `context.md` | Diagnóstico completo com todos os problemas identificados |
| `index.php` | Entender o roteamento atual antes de modificar qualquer fluxo |
| `connection.php` | Entender a conexão antes da Etapa 2 |
| `View/menuView.php` | Entender a estrutura das views antes da Etapa 7 |
