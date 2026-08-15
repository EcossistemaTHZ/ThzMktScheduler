import { AccountCircle } from '@mui/icons-material';
import { AppBar, Box, Button, IconButton, MenuItem, TextField, Toolbar, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';
import { NavLink, useLocation } from 'react-router-dom';
import type { ReactNode } from 'react';
import { SUPPORTED_LANGUAGES } from '../i18n';

function NavButton({ to, label, end }: { to: string; label: string; end?: boolean }) {
  const location = useLocation();
  const active = end ? location.pathname === to : location.pathname.startsWith(to);

  return (
    <Button
      color="inherit"
      component={NavLink}
      to={to}
      end={end}
      sx={
        active
          ? {
              background: 'rgba(255,255,255,0.12)',
              borderBottom: '2px solid #fff',
              borderRadius: 0,
            }
          : undefined
      }
    >
      {label}
    </Button>
  );
}

export function Layout({ children }: { children: ReactNode }) {
  const { t, i18n } = useTranslation();

  return (
    <Box>
      <AppBar position="static">
        <Toolbar>
          <Typography variant="h6" sx={{ fontWeight: 'bold', letterSpacing: 1, mr: 3 }}>
            MktScheduler
          </Typography>

          <NavButton to="/" label={t('APP.DASHBOARD')} end />
          <NavButton to="/users" label={t('APP.USERS_AND_DESTINATIONS')} />

          <Box sx={{ flexGrow: 1 }} />

          <TextField
            select
            size="small"
            value={i18n.language}
            onChange={(e) => i18n.changeLanguage(e.target.value)}
            sx={{
              minWidth: 90,
              mr: 1,
              '& .MuiInputBase-root': { color: '#fff' },
              '& .MuiSvgIcon-root': { color: '#fff' },
              '& .MuiOutlinedInput-notchedOutline': { borderColor: 'rgba(255,255,255,0.5)' },
              '&:hover .MuiOutlinedInput-notchedOutline': { borderColor: '#fff' },
            }}
            slotProps={{ input: { 'aria-label': 'language' } }}
          >
            {SUPPORTED_LANGUAGES.map((lang) => (
              <MenuItem key={lang} value={lang}>
                {lang === 'pt-BR' ? 'PT' : 'EN'}
              </MenuItem>
            ))}
          </TextField>

          <IconButton color="inherit" aria-label="account">
            <AccountCircle />
          </IconButton>
        </Toolbar>
      </AppBar>

      <Box sx={{ maxWidth: 1200, margin: '0 auto', padding: '24px' }}>{children}</Box>
    </Box>
  );
}