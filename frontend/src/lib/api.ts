import type { ApiMessage, Campaign, CampaignInput, User, UserInput } from './types';

export const API_URL: string = import.meta.env.VITE_API_URL ?? '/api';

export class ApiError extends Error {
  constructor(
    message: string,
    public readonly status: number,
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(`${API_URL}${path}`, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  });

  let body: unknown = null;
  try {
    body = await res.json();
  } catch {
    // Resposta sem corpo JSON
  }

  if (!res.ok) {
    const message = (body as { error?: string } | null)?.error ?? `Request failed (${res.status})`;
    throw new ApiError(message, res.status);
  }

  return body as T;
}

export function getCampaigns(): Promise<Campaign[]> {
  return request<Campaign[]>('/campaigns');
}

export function getCampaign(id: number): Promise<Campaign> {
  return request<Campaign>(`/campaigns/${id}`);
}

export function createCampaign(data: CampaignInput): Promise<ApiMessage> {
  return request<ApiMessage>('/campaigns', { method: 'POST', body: JSON.stringify(data) });
}

export function updateCampaign(id: number, data: CampaignInput): Promise<ApiMessage> {
  return request<ApiMessage>(`/campaigns/${id}`, { method: 'PUT', body: JSON.stringify(data) });
}

export function deleteCampaign(id: number): Promise<ApiMessage> {
  return request<ApiMessage>(`/campaigns/${id}`, { method: 'DELETE' });
}

export function getUsers(): Promise<User[]> {
  return request<User[]>('/users');
}

export function getUser(id: number): Promise<User> {
  return request<User>(`/users/${id}`);
}

export function createUser(data: UserInput): Promise<ApiMessage> {
  return request<ApiMessage>('/users', { method: 'POST', body: JSON.stringify(data) });
}

export function updateUser(id: number, data: UserInput): Promise<ApiMessage> {
  return request<ApiMessage>(`/users/${id}`, { method: 'PUT', body: JSON.stringify(data) });
}

export function deleteUser(id: number): Promise<ApiMessage> {
  return request<ApiMessage>(`/users/${id}`, { method: 'DELETE' });
}