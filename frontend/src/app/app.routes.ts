import { Routes } from '@angular/router';
import { Component } from '@angular/core';

import { CampaignListComponent } from './components/campaign-list/campaign-list.component';
import { CampaignFormComponent } from './components/campaign-form/campaign-form.component';
import { UserManagerComponent } from './components/user-manager/user-manager.component';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CampaignListComponent, CampaignFormComponent],
  template: `
    <app-campaign-form #form (statusChanged)="list.loadCampaigns()"></app-campaign-form>
    <app-campaign-list #list (edit)="form.edit($event)"></app-campaign-list>
  `
})
export class DashboardComponent { }

export const routes: Routes = [
  { path: '', component: DashboardComponent },
  { path: 'users', component: UserManagerComponent },
  { path: '**', redirectTo: '' }
];
