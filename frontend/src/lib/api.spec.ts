import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { ApiError, createCampaign, getCampaigns } from './api';

function jsonResponse(body: unknown, status = 200): Response {
  return new Response(JSON.stringify(body), {
    status,
    headers: { 'Content-Type': 'application/json' },
  });
}

describe('api', () => {
  const fetchMock = vi.fn<typeof fetch>();

  beforeEach(() => {
    vi.stubGlobal('fetch', fetchMock);
  });

  afterEach(() => {
    vi.unstubAllGlobals();
    fetchMock.mockReset();
  });

  it('getCampaigns retorna os dados parseados', async () => {
    const campaigns = [
      { id: 1, subject: 'Promo', message: 'x', scheduled_at: '2026-08-15T14:30', status: 'pending' },
    ];
    fetchMock.mockResolvedValue(jsonResponse(campaigns));

    await expect(getCampaigns()).resolves.toEqual(campaigns);
    expect(fetchMock).toHaveBeenCalledWith('/api/campaigns', expect.objectContaining({}));
  });

  it('createCampaign envia POST com corpo JSON', async () => {
    fetchMock.mockResolvedValue(jsonResponse({ message: 'ok', id: 2 }, 201));

    const payload = { subject: 'Teste', message: 'msg', scheduled_at: '2026-08-15T14:30' };
    await expect(createCampaign(payload)).resolves.toEqual({ message: 'ok', id: 2 });

    const [, options] = fetchMock.mock.calls[0];
    expect(options?.method).toBe('POST');
    expect(options?.body).toBe(JSON.stringify(payload));
  });

  it('lança ApiError com a mensagem do servidor em respostas de erro', async () => {
    fetchMock.mockResolvedValue(jsonResponse({ error: 'E-mail já está cadastrado' }, 409));

    const promise = getCampaigns();
    await expect(promise).rejects.toMatchObject({
      name: 'ApiError',
      status: 409,
      message: 'E-mail já está cadastrado',
    });
  });

  it('lança ApiError genérico quando o corpo não tem mensagem', async () => {
    fetchMock.mockResolvedValue(jsonResponse({}, 500));

    await expect(getCampaigns()).rejects.toBeInstanceOf(ApiError);
  });
});