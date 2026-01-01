# Marketing Campaign Manager

A PHP 8.x + Angular application for managing marketing campaigns.

## Prerequisites
- PHP 8.2 or higher
- Node.js & npm
- SQLite extension for PHP

## Setup

### Backend
1. Navigate to `backend/`
2. Run database migration (auto-handled on first run)
3. Start the server:
   ```bash
   cd backend/public
   php -S localhost:8000
   ```

### Frontend
1. Navigate to `frontend/`
2. Install dependencies (should be done automatically):
   ```bash
   npm install
   ```
3. Start the dev server:
   ```bash
   ng serve
   ```
   or
   ```bash
   npm start
   ```

4. Open `http://localhost:4200`

## Features
- Create, View, Delete Campaigns
- Schedule dates
- Clean Tailwind UI
