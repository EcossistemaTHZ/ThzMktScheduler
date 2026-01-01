import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../services/api.service';
import { User } from '../../models/campaign.model';

@Component({
    selector: 'app-user-manager',
    standalone: true,
    imports: [CommonModule, FormsModule],
    template: `
    <div class="space-y-6">
      <!-- Create User -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Add User / Destination</h2>
        <form (ngSubmit)="onSubmit()" class="flex gap-4 items-end">
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" [(ngModel)]="newUser.name" name="name" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
          </div>
          <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" [(ngModel)]="newUser.email" name="email" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2">
          </div>
          <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            Add
          </button>
        </form>
      </div>

      <!-- List -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Registered Users</h2>
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr *ngFor="let user of users">
              <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
            </tr>
            <tr *ngIf="users.length === 0">
               <td colspan="2" class="px-6 py-4 text-center text-gray-500">No users found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `
})
export class UserManagerComponent implements OnInit {
    users: User[] = [];
    newUser: User = { name: '', email: '' };

    constructor(private api: ApiService) { }

    ngOnInit(): void {
        this.loadUsers();
    }

    loadUsers(): void {
        this.api.getUsers().subscribe(data => this.users = data);
    }

    onSubmit(): void {
        if (!this.newUser.name || !this.newUser.email) return;

        this.api.createUser(this.newUser).subscribe({
            next: () => {
                this.loadUsers();
                this.newUser = { name: '', email: '' };
            },
            error: (err) => alert('Error creating user')
        });
    }
}
