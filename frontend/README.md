# Frontend — React 19 + Vite

Frontend do MktScheduler em **React 19**, **TypeScript**, **Vite**, **MUI**, **TanStack Query**, **React Hook Form + Zod** e **react-i18next**.

## Scripts

```bash
npm install     # instala dependências
npm run dev     # dev server em http://localhost:4200 (proxy /api → http://localhost:18000)
npm run build   # typecheck (tsc -b) + build de produção
npm run preview # serve o build localmente
npm test        # testes (Vitest + Testing Library)
```

## Proxy da API

Em dev, o Vite encaminha `/api/*` para o backend PHP. O alvo padrão é `http://localhost:18000`
e pode ser alterado com a variável de ambiente `VITE_API_TARGET`:

```bash
VITE_API_TARGET=http://localhost:18000 npm run dev
```

Em produção, defina `VITE_API_URL` (ex.: `https://api.exemplo.com/api`) no build, ou sirva o
frontend pelo mesmo host do backend para usar o caminho relativo `/api`.

## Estrutura

```
src/
  assets/i18n/        recursos de tradução (pt-BR, en)
  components/         Layout, CampaignForm, CampaignList, UserManager, ConfirmDialog, FeedbackProvider
  lib/                api (fetch tipado), types, format
  pages/              DashboardPage, UsersPage
  main.tsx            bootstrap (QueryClient + Router + i18n)
  App.tsx             rotas
```