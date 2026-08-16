# Marketing Campaign Scheduler

Aplicação de gerenciamento de campanhas de marketing: backend **PHP 8.2+ puro** (padrões PSR) + frontend **React 19** (MUI).

## Stack backend

PHP puro sem framework, seguindo os padrões PSR da indústria:

| PSR | Uso |
|---|---|
| PSR-1 / PSR-12 | Estilo de código (PHP-CS-Fixer) |
| PSR-3 | Logging via Monolog |
| PSR-4 | Autoload via Composer (`App\` => `src/`) |
| PSR-7 | Mensagens HTTP (nyholm/psr7) |
| PSR-11 | Container de dependência (PHP-DI) |
| PSR-15 | Middleware e handlers (RequestHandlerInterface) |
| PSR-17 | Factories HTTP (nyholm/psr7) |

### Estrutura

```
backend/
  public/index.php       # Front controller (PSR-7)
  bin/migrate.php        # Runner de migrations (CLI)
  src/
    Action/              # Handlers PSR-15 (thin, só HTTP)
    Service/             # Regras de negócio
    Repository/          # Acesso a dados (PDO)
    Validation/          # Validação (regras reutilizáveis)
    Middleware/          # Cors, JsonBodyParser, ErrorHandler
    Http/                # RequestFactory, JsonResponder, ResponseEmitter
    Core/                # Router, MiddlewarePipeline, Container, Application
  migrations/{driver}/   # SQL versionado por driver (sqlite|postgres|mysql)
  tests/                 # PHPUnit
```

## Quick start

### Windows
1. `setup-php.bat` — baixa o PHP local para `.tools\php`.
2. `start.bat` — aplica migrations, sobe o backend (porta 18000) e o frontend (porta 4200).

Se o PHP local não existir, `start.bat` usa o PHP instalado no PATH.

### Linux / macOS (e qualquer SO com Node + PHP)
```bash
# Backend
php .tools/composer.phar install --working-dir=backend   # ou composer install
php backend/bin/migrate.php                             # cria/atualiza as tabelas

# Frontend
npm install
npm install --prefix frontend
npm run dev      # sobe backend (PHP) e frontend (Vite) + roda migrations no boot
```

O script `scripts/start-backend.js` resolve o PHP automaticamente: usa `.tools/php/php.exe`
no Windows e `php` do PATH nos demais sistemas.

## Configuração (backend/.env)

Copie `backend/.env.example` para `backend/.env` e ajuste:

| Variável | Padrão | Descrição |
|---|---|---|
| `APP_ENV` | `dev` | `dev` loga em stderr; `prod` loga em arquivo |
| `APP_DEBUG` | `false` | Inclui detalhes da exceção na resposta 500 |
| `DB_DSN` | `sqlite:backend/database.sqlite` | SQLite em dev; `pgsql:` / `mysql:` em prod |
| `DB_USER` / `DB_PASS` | – | Credenciais (PostgreSQL/MySQL) |
| `CORS_ORIGIN` | `*` | Origem permitida no CORS |
| `LOG_PATH` | `backend/var/logs/app.log` | Arquivo de log (prod) |

Migrações versionadas em `backend/migrations/{driver}/` (sqlite, postgres e mysql já incluídas).

## Testes e qualidade

```bash
# Backend
php vendor/bin/phpunit          # testes
php vendor/bin/phpstan analyse  # análise estática (nível 5)
php vendor/bin/php-cs-fixer fix # estilo PSR-12

# Frontend
cd frontend && npm test
```

## API (v1)

- `GET|POST /api/v1/campaigns`, `GET|PUT|DELETE /api/v1/campaigns/{id}`
- `GET|POST /api/v1/users`, `GET|PUT|DELETE /api/v1/users/{id}`
- Erros em JSON `{ "error": "...", "errors": [...] }` com códigos HTTP adequados (400/404/405/409/500).
