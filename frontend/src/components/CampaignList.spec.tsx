import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { render, screen } from '@testing-library/react';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import type { ReactNode } from 'react';
import { getCampaigns } from '../lib/api';
import type { Campaign } from '../lib/types';
import { CampaignList } from './CampaignList';
import { FeedbackProvider } from './FeedbackProvider';

vi.mock('../lib/api', () => ({
  getCampaigns: vi.fn(),
  deleteCampaign: vi.fn(),
}));

const mockedGetCampaigns = vi.mocked(getCampaigns);

function renderWithProviders(ui: ReactNode) {
  const queryClient = new QueryClient({
    defaultOptions: { queries: { retry: false } },
  });
  return render(
    <QueryClientProvider client={queryClient}>
      <FeedbackProvider>{ui}</FeedbackProvider>
    </QueryClientProvider>,
  );
}

const CAMPAIGNS: Campaign[] = [
  { id: 1, subject: 'Promo de verão', message: 'msg', scheduled_at: '2026-08-15T14:30', status: 'pending' },
  { id: 2, subject: 'Lançamento', message: 'msg', scheduled_at: '2026-08-20T10:00', status: 'sent' },
];

describe('CampaignList', () => {
  beforeEach(() => {
    mockedGetCampaigns.mockReset();
  });

  afterEach(() => {
    vi.clearAllMocks();
  });

  it('renderiza as campanhas retornadas pela API', async () => {
    mockedGetCampaigns.mockResolvedValue(CAMPAIGNS);

    renderWithProviders(<CampaignList onEdit={() => {}} />);

    expect(await screen.findByText('Promo de verão')).toBeInTheDocument();
    expect(screen.getByText('Lançamento')).toBeInTheDocument();
  });

  it('mostra a mensagem de vazio quando não há campanhas', async () => {
    mockedGetCampaigns.mockResolvedValue([]);

    renderWithProviders(<CampaignList onEdit={() => {}} />);

    expect(
      await screen.findByText('Nenhuma campanha agendada. Crie uma para começar.'),
    ).toBeInTheDocument();
  });
});