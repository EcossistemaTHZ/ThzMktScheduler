/**
 * Campaign interface
 * @interface Campaign
 * @property {number} [id] - The campaign ID
 * @property {string} subject - The campaign subject
 * @property {string} message - The campaign message
 * @property {string} scheduled_at - The campaign scheduled date
 * @property {'pending' | 'sent' | 'failed'} status - The campaign status
 * @property {string} [created_at] - The campaign creation date
 */
export interface Campaign {
    id?: number;
    subject: string;
    message: string;
    scheduled_at: string;
    status: 'pending' | 'sent' | 'failed';
    created_at?: string;
}

/**
 * User interface
 * @interface User
 * @property {number} [id] - The user ID
 * @property {string} name - The user name
 * @property {string} email - The user email
 */
export interface User {
    id?: number;
    name: string;
    email: string;
}
