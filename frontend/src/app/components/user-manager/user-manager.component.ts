import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { ApiService } from '../../services/api.service';
import { User } from '../../models/campaign.model';

@Component({
  selector: 'app-user-manager',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatTableModule,
    MatCardModule,
    MatIconModule
  ],
  templateUrl: './user-manager.component.html',
  styleUrls: ['./user-manager.component.css']
})
export class UserManagerComponent implements OnInit {
  users: User[] = [];
  newUser: User = { name: '', email: '' };
  displayedColumns: string[] = ['name', 'email'];

  constructor(private readonly api: ApiService) { }

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
