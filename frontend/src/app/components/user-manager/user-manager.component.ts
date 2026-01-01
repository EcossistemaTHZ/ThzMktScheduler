import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatTableModule, MatTableDataSource } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
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
    MatSnackBarModule,
    TranslateModule
  ],
  templateUrl: './user-manager.component.html',
  styleUrls: ['./user-manager.component.css']
})
export class UserManagerComponent implements OnInit {
  users: User[] = [];
  dataSource = new MatTableDataSource<User>([]);
  newUser: User = { name: '', email: '' };
  displayedColumns: string[] = ['name', 'email', 'actions'];
  isEditing = false;
  editingUserId?: number;

  constructor(
    private readonly api: ApiService,
    private readonly translate: TranslateService,
    private readonly snackBar: MatSnackBar
  ) { }

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(): void {
    this.api.getUsers().subscribe({
      next: (data) => {
        this.users = data;
        this.dataSource.data = [...data];
      },
      error: () => this.showFeedback(this.translate.instant('ERRORS.LOAD_DATA'), true)
    });
  }

  onSubmit(): void {
    if (!this.newUser.name || !this.newUser.email) return;

    if (this.isEditing && this.editingUserId) {
      this.api.updateUser(this.editingUserId, this.newUser).subscribe({
        next: () => {
          this.showFeedback(this.translate.instant('SUCCESS.USER_UPDATE'));
          this.loadUsers();
          this.cancelEdit();
        },
        error: () => this.showFeedback(this.translate.instant('ERRORS.USER_UPDATE'), true)
      });
    } else {
      this.api.createUser(this.newUser).subscribe({
        next: () => {
          this.showFeedback(this.translate.instant('SUCCESS.USER_CREATE'));
          this.loadUsers();
          this.newUser = { name: '', email: '' };
        },
        error: () => this.showFeedback(this.translate.instant('ERRORS.USER_CREATE'), true)
      });
    }
  }

  onEdit(user: User): void {
    this.isEditing = true;
    this.editingUserId = user.id;
    this.newUser = { ...user };
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  cancelEdit(): void {
    this.isEditing = false;
    this.editingUserId = undefined;
    this.newUser = { name: '', email: '' };
  }

  onDelete(id: number): void {
    if (confirm(this.translate.instant('COMMON.CONFIRM_DELETE'))) {
      this.api.deleteUser(id).subscribe({
        next: () => {
          this.showFeedback(this.translate.instant('SUCCESS.USER_DELETE'));
          this.loadUsers();
        },
        error: () => this.showFeedback(this.translate.instant('ERRORS.USER_DELETE'), true)
      });
    }
  }

  private showFeedback(message: string, isError = false): void {
    this.snackBar.open(message, this.translate.instant('COMMON.CLOSE'), {
      duration: 3000,
      panelClass: isError ? ['error-snackbar'] : ['success-snackbar'],
      horizontalPosition: 'end',
      verticalPosition: 'top'
    });
  }
}
