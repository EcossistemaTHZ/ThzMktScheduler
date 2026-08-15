import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import ptBR from './assets/i18n/pt-BR.json';
import en from './assets/i18n/en.json';

export const SUPPORTED_LANGUAGES = ['pt-BR', 'en'] as const;
export type Language = (typeof SUPPORTED_LANGUAGES)[number];

i18n.use(initReactI18next).init({
  resources: {
    'pt-BR': { translation: ptBR },
    en: { translation: en },
  },
  lng: 'pt-BR',
  fallbackLng: 'pt-BR',
  interpolation: { escapeValue: false },
});

export default i18n;