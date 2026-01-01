import { Component } from '@angular/core';
import { RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-root',
    standalone: true,
    imports: [RouterOutlet, CommonModule, RouterLink, RouterLinkActive],
    template: `
    <div class="min-h-screen bg-gray-100 font-sans">
      <!-- Navbar -->
      <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <div class="flex-shrink-0 flex items-center">
                <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                  MktScheduler
                </span>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <a routerLink="/" routerLinkActive="text-blue-600" [routerLinkActiveOptions]="{exact: true}" 
                 class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition cursor-pointer">
                 Dashboard
              </a>
              <a routerLink="/users" routerLinkActive="text-blue-600"
                 class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition cursor-pointer">
                 Users & Destinations
              </a>
              <div class="ml-3 relative">
                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                  A
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main Content -->
      <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <router-outlet></router-outlet>
      </main>
    </div>
  `
})
export class AppComponent {
    title = 'frontend';
}
