export type CampaignStatus = 'pending' | 'sent' | 'failed';

export interface Campaign {
  id?: number;
  subject: string;
  message: string;
  scheduled_at: string;
  status: CampaignStatus;
  created_at?: string;
}

export type CampaignInput = Pick<Campaign, 'subject' | 'message' | 'scheduled_at'> &
  Partial<Pick<Campaign, 'status'>>;

export interface User {
  id?: number;
  name: string;
  email: string;
}

export type UserInput = Pick<User, 'name' | 'email'>;

export interface ApiMessage {
  message: string;
  id?: number;
}