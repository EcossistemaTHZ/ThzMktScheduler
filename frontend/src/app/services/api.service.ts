import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Campaign, User } from '../models/campaign.model';

@Injectable({
    providedIn: 'root'
})
export class ApiService {
    private readonly apiUrl = 'http://localhost:8000/api'; // PHP Backend URL

    constructor(private readonly http: HttpClient) { }

    // Lista as campanhas
    getCampaigns(): Observable<Campaign[]> {
        return this.http.get<Campaign[]>(`${this.apiUrl}/campaigns`);
    }

    // Cria uma campanha
    createCampaign(campaign: Campaign): Observable<any> {
        return this.http.post(`${this.apiUrl}/campaigns`, campaign);
    }
    
    // Atualiza uma campanha
    updateCampaign(id: number, campaign: Campaign): Observable<any> {
        return this.http.put(`${this.apiUrl}/campaigns/${id}`, campaign);
    }
    
    // Deleta uma campanha
    deleteCampaign(id: number): Observable<any> {
        return this.http.delete(`${this.apiUrl}/campaigns/${id}`);
    }

    // Lista os usuários
    getUsers(): Observable<User[]> {
        return this.http.get<User[]>(`${this.apiUrl}/users`);
    }
    
    // Cria um usuário
    createUser(user: User): Observable<any> {
        return this.http.post(`${this.apiUrl}/users`, user);
    }
}
