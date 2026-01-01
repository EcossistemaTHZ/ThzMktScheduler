import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { ApiService } from '../../services/api.service';
import { Campaign } from '../../models/campaign.model';

@Component({
  selector: 'app-campaign-form',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatCardModule,
    MatIconModule
  ],
  templateUrl: './campaign-form.component.html',
  styleUrls: ['./campaign-form.component.css']
})
export class CampaignFormComponent {
  @Output() created = new EventEmitter<void>();

  model: Campaign = {
    subject: '',
    message: '',
    scheduled_at: '',
    status: 'pending'
  };

  constructor(private readonly api: ApiService) { }

  onSubmit(): void {
    this.api.createCampaign(this.model).subscribe({
      next: () => {
        this.created.emit();
        this.resetForm();
      },
      error: (err) => alert('Error creating campaign')
    });
  }

  resetForm(): void {
    this.model = {
      subject: '',
      message: '',
      scheduled_at: '',
      status: 'pending'
    };
  }
}
