import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Campaign, User } from '../models/campaign.model';

@Injectable({
    providedIn: 'root'
})
export class ApiService {
    private apiUrl = 'http://localhost:8000/api'; // PHP Backend URL

    constructor(private http: HttpClient) { }

    // Campaigns
    getCampaigns(): Observable<Campaign[]> {
        return this.http.get<Campaign[]>(`${this.apiUrl}/campaigns`);
    }

    createCampaign(campaign: Campaign): Observable<any> {
        return this.http.post(`${this.apiUrl}/campaigns`, campaign);
    }

    deleteCampaign(id: number): Observable<any> {
        return this.http.delete(`${this.apiUrl}/campaigns/${id}`);
    }

    // Users
    getUsers(): Observable<User[]> {
        return this.http.get<User[]>(`${this.apiUrl}/users`);
    }

    createUser(user: User): Observable<any> {
        return this.http.post(`${this.apiUrl}/users`, user);
    }
}
