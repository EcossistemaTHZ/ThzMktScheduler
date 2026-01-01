import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { ApiService } from '../../services/api.service';
import { Campaign } from '../../models/campaign.model';

@Component({
  selector: 'app-campaign-list',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MatTableModule,
    MatCardModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule
  ],
  templateUrl: './campaign-list.component.html',
  styleUrls: ['./campaign-list.component.css']
})
export class CampaignListComponent implements OnInit {
  campaigns: Campaign[] = [];
  displayedColumns: string[] = ['subject', 'scheduled_at', 'destinations', 'status', 'actions'];

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
