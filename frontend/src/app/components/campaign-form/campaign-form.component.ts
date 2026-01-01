import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { Campaign } from '../../models/campaign.model';

@Component({
    selector: 'app-campaign-form',
    standalone: true,
    imports: [CommonModule, FormsModule],
    template: `
    <div class="bg-white shadow rounded-lg p-6 mb-8 border-l-4 border-blue-500">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Create New Campaign</h2>
      <form (ngSubmit)="onSubmit()" #form="ngForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input type="text" [(ngModel)]="model.subject" name="subject" required 
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                   placeholder="Enter email subject">
          </div>
          
          <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Message Content</label>
            <textarea [(ngModel)]="model.message" name="message" required rows="4"
                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"
                      placeholder="Type your marketing message..."></textarea>
          </div>

          <div>
             <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Date</label>
             <input type="datetime-local" [(ngModel)]="model.scheduled_at" name="scheduled_at" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border">
          </div>
          
          <div class="flex items-end">
            <button type="submit" [disabled]="!form.valid"
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-colors">
              Schedule Campaign
            </button>
          </div>
        </div>
      </form>
    </div>
  `
})
export class CampaignFormComponent {
    @Output() created = new EventEmitter<void>();

    model: Campaign = {
        subject: '',
        message: '',
        scheduled_at: '',
        status: 'pending'
    };

    constructor(private api: ApiService) { }

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
