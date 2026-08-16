const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const backendDir = path.join(root, 'backend');

let php = process.env.PHP_BIN || 'php';
if (process.platform === 'win32' && !process.env.PHP_BIN) {
  const localPhp = path.join(root, '.tools', 'php', 'php.exe');
  if (fs.existsSync(localPhp)) {
    php = localPhp;
  }
}

const migrate = spawn(php, ['bin/migrate.php'], {
  cwd: backendDir,
  stdio: 'inherit',
  shell: process.platform === 'win32',
});

migrate.on('exit', (migrateCode) => {
  if (migrateCode !== 0) {
    console.error('Migração falhou. Verifique o banco de dados e o arquivo backend/.env.');
    process.exit(migrateCode ?? 1);
  }

  console.log(`Backend PHP rodando em http://localhost:18000 (${php})`);

  const child = spawn(php, ['-S', 'localhost:18000', '-t', 'public'], {
    cwd: backendDir,
    stdio: 'inherit',
    shell: process.platform === 'win32',
  });

  child.on('error', (err) => {
    if (err.code === 'ENOENT') {
      console.error(`PHP não encontrado (${php}).`);
      console.error('Instale o PHP 8.2+ e adicione ao PATH, ou defina a variável PHP_BIN apontando para o executável.');
      console.error('Exemplo:  PHP_BIN=/usr/bin/php8.2 npm run dev');
      console.error('No Windows, rode setup-php.bat para baixar automaticamente.');
    } else {
      console.error('Falha ao iniciar o PHP:', err.message);
    }
    process.exit(1);
  });

  child.on('exit', (code) => {
    process.exit(code ?? 1);
  });
});

migrate.on('error', (err) => {
  console.error('Falha ao executar migrações:', err.message);
  process.exit(1);
});
