import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { TranslateModule, TranslateService } from '@ngx-translate/core';
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
    MatIconModule,
    TranslateModule
  ],
  templateUrl: './user-manager.component.html',
  styleUrls: ['./user-manager.component.css']
})
export class UserManagerComponent implements OnInit {
  users: User[] = [];
  newUser: User = { name: '', email: '' };
  displayedColumns: string[] = ['name', 'email', 'actions'];
  isEditing = false;
  editingUserId?: number;

  constructor(
    private readonly api: ApiService,
    private readonly translate: TranslateService
  ) { }

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.api.getUsers().subscribe(data => this.users = data);
  }

  onSubmit(): void {
    if (!this.newUser.name || !this.newUser.email) return;

    if (this.isEditing && this.editingUserId) {
      this.api.updateUser(this.editingUserId, this.newUser).subscribe({
        next: () => {
          this.loadUsers();
          this.cancelEdit();
        },
        error: () => alert(this.translate.instant('ERRORS.USER_UPDATE'))
      });
    } else {
      this.api.createUser(this.newUser).subscribe({
        next: () => {
          this.loadUsers();
          this.newUser = { name: '', email: '' };
        },
        error: () => alert(this.translate.instant('ERRORS.USER_CREATE'))
      });
    }
  }

  onEdit(user: User): void {
    this.isEditing = true;
    this.editingUserId = user.id;
    this.newUser = { ...user };
  }

  cancelEdit(): void {
    this.isEditing = false;
    this.editingUserId = undefined;
    this.newUser = { name: '', email: '' };
  }

  onDelete(id: number): void {
    if (confirm(this.translate.instant('COMMON.CONFIRM_DELETE'))) {
      this.api.deleteUser(id).subscribe({
        next: () => this.loadUsers(),
        error: () => alert(this.translate.instant('ERRORS.USER_DELETE'))
      });
    }
  }
}
