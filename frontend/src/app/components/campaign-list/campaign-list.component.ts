import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
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
    MatChipsModule,
    TranslateModule
  ],
  templateUrl: './campaign-list.component.html',
  styleUrls: ['./campaign-list.component.css']
})
export class CampaignListComponent implements OnInit {
  campaigns: Campaign[] = [];
  displayedColumns: string[] = ['subject', 'scheduled_at', 'destinations', 'status', 'actions'];

  /**
   * Constructor
   * @param api The API service
   * @param translate The translation service
   */
  constructor(
    private readonly api: ApiService,
    private readonly translate: TranslateService
  ) { }

  /**
   * OnInit lifecycle hook
   * Carrega as campanhas ao iniciar o componente
   */
  ngOnInit(): void {
    this.loadCampaigns();
  }
  /**
   * Load campaigns from the API
   * Carrega as campanhas da API
   * @returns void
   */
  loadCampaigns(): void {
    this.api.getCampaigns().subscribe({
      next: (data: Campaign[]) => {
        this.campaigns = data;
      },
      error: (err: any) => {
        console.error('Error loading campaigns', err);
      }
    });
  }

  /**
   * Delete a campaign from the API
   * @param id The ID of the campaign to delete
   * @returns void
   */
  deleteCampaign(id: number): void {
    if (confirm(this.translate.instant('COMMON.CONFIRM_DELETE'))) {
      this.api.deleteCampaign(id).subscribe({
        next: () => this.loadCampaigns(),
        error: (err: any) => console.error('Error deleting campaign', err)
      });
    }
  }
}
