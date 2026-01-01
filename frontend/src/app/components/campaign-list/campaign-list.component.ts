import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
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
    MatSnackBarModule,
    TranslateModule
  ],
  templateUrl: './campaign-list.component.html',
  styleUrls: ['./campaign-list.component.css']
})
export class CampaignListComponent implements OnInit {
  campaigns: Campaign[] = [];
  displayedColumns: string[] = ['subject', 'scheduled_at', 'destinations', 'status', 'actions'];

  constructor(
    private readonly api: ApiService,
    private readonly translate: TranslateService,
    private readonly snackBar: MatSnackBar
  ) { }

  ngOnInit(): void {
    this.loadCampaigns();
  }

  loadCampaigns(): void {
    this.api.getCampaigns().subscribe({
      next: (data: Campaign[]) => {
        this.campaigns = data;
      },
      error: (err: any) => {
        console.error('Error loading campaigns', err);
        this.snackBar.open(
          this.translate.instant('ERRORS.LOAD_DATA'),
          this.translate.instant('COMMON.CLOSE'),
          { duration: 3000, panelClass: ['error-snackbar'], horizontalPosition: 'end', verticalPosition: 'top' }
        );
      }
    });
  }

  deleteCampaign(id: number): void {
    if (confirm(this.translate.instant('COMMON.CONFIRM_DELETE'))) {
      this.api.deleteCampaign(id).subscribe({
        next: () => {
          this.snackBar.open(
            this.translate.instant('SUCCESS.CAMPAIGN_DELETE'),
            this.translate.instant('COMMON.CLOSE'),
            { duration: 3000, horizontalPosition: 'end', verticalPosition: 'top' }
          );
          this.loadCampaigns();
        },
        error: (err: any) => {
          this.snackBar.open(
            this.translate.instant('ERRORS.CAMPAIGN_DELETE'),
            this.translate.instant('COMMON.CLOSE'),
            { duration: 3000, panelClass: ['error-snackbar'], horizontalPosition: 'end', verticalPosition: 'top' }
          );
        }
      });
    }
  }
}
