export interface Campaign {
    id?: number;
    subject: string;
    message: string;
    scheduled_at: string;
    status: 'pending' | 'sent' | 'failed';
    created_at?: string;
}

export interface User {
    id?: number;
    name: string;
    email: string;
}
