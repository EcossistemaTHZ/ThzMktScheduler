import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { Campaign } from '../../models/campaign.model';

@Component({
    selector: 'app-campaign-list',
    standalone: true,
    imports: [CommonModule, FormsModule],
    template: `
    <div class="bg-white shadow rounded-lg p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-800">Scheduled Campaigns</h2>
        <!-- Filter or Actions could go here -->
      </div>
      
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm whitespace-nowrap">
          <thead class="uppercase tracking-wider border-b-2 border-gray-200 bg-gray-50 text-gray-600">
            <tr>
              <th scope="col" class="px-6 py-4">Subject</th>
              <th scope="col" class="px-6 py-4">Scheduled At</th>
              <th scope="col" class="px-6 py-4">Destinations</th>
              <th scope="col" class="px-6 py-4">Status</th>
              <th scope="col" class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr *ngFor="let campaign of campaigns" class="border-b hover:bg-gray-50 transition">
              <td class="px-6 py-4 font-medium">{{ campaign.subject }}</td>
              <td class="px-6 py-4">{{ campaign.scheduled_at | date:'medium' }}</td>
              <td class="px-6 py-4 text-gray-500">All Users (Demo)</td>
              <td class="px-6 py-4">
                <span [ngClass]="{
                  'bg-yellow-100 text-yellow-800': campaign.status === 'pending',
                  'bg-green-100 text-green-800': campaign.status === 'sent',
                  'bg-red-100 text-red-800': campaign.status === 'failed'
                }" class="px-2 py-1 rounded text-xs font-semibold">
                  {{ campaign.status | titlecase }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <button (click)="deleteCampaign(campaign.id!)" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
              </td>
            </tr>
            <tr *ngIf="campaigns.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                No campaigns scheduled. Create one to get started.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `
})
export class CampaignListComponent implements OnInit {
    campaigns: Campaign[] = [];

    constructor(private api: ApiService) { }

    ngOnInit(): void {
        this.loadCampaigns();
    }

    loadCampaigns(): void {
        this.api.getCampaigns().subscribe({
            next: (data) => this.campaigns = data,
            error: (err) => console.error('Error loading campaigns', err)
        });
    }

    deleteCampaign(id: number): void {
        if (confirm('Are you sure?')) {
            this.api.deleteCampaign(id).subscribe(() => this.loadCampaigns());
        }
    }
}
