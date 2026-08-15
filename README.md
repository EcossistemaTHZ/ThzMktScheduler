# Marketing Campaign Manager

Aplicação de gerenciamento de campanhas de marketing: backend **PHP 8.x** + frontend **React 19**.

## Quick start

### Windows
1. `setup-php.bat` — baixa o PHP local para `.tools\php`.
2. `start.bat` — sobe o backend (porta 18000) e o frontend (porta 4200).

Se o PHP local não existir, `start.bat` usa o PHP instalado no PATH.

### Linux / macOS (e qualquer SO com Node + PHP)
```bash
npm install        # instala dependências raiz (concurrently)
npm install --prefix frontend
npm run dev        # sobe backend (PHP) e frontend (Vite)
```

O script `scripts/start-backend.js` resolve o PHP automaticamente: usa `.tools/php/php.exe`
no Windows e `php` do PATH nos demais sistemas.

## Requisitos
- Node.js & npm
- PHP 8.2+ (ou rode `setup-php.bat` no Windows para baixar automaticamente)

## Setup manual

### Backend
```bash
cd backend/public
php -S localhost:18000
```
As tabelas são criadas automaticamente no primeiro acesso (`database.sqlite`).
Configuração opcional via variáveis de ambiente: `DB_DSN` (ex.: `sqlite:./meu.db`)
e `CORS_ORIGIN` (origem permitida; default `*`).

### Frontend
```bash
cd frontend
npm install
npm start          # http://localhost:4200
```

## Funcionalidades
- CRUD de Campanhas (criar, editar, agendar, listar, excluir)
- CRUD de Usuários / Destinos
- Validação no backend (e-mail, data de agendamento, status) e no frontend (React Hook Form + Zod)
- i18n pt-BR / en (seletor no topo)
- UI MUI (Material)

## Testes
```bash
cd frontend && npm test
```

## API
- `GET|POST /api/campaigns`, `GET|PUT|DELETE /api/campaigns/{id}`
- `GET|POST /api/users`, `GET|PUT|DELETE /api/users/{id}`
- Erros retornam JSON com `{ "error": "..." }` e códigos HTTP adequados (400/404/405/409/500).