import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../core/services/auth';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-login',
  imports: [FormsModule, CommonModule],
  templateUrl: './login.html',
  styleUrl: './login.css',
})
export class Login {
  email = '';
  password = '';
  error = '';

  constructor(
    private authService: AuthService,
    private router: Router
  ) {}

  onSubmit(): void {
    this.error = '';
    console.log('login clicked');

    this.authService.login(this.email, this.password).subscribe({
      next: () => {
        console.log('login success');
        this.router.navigate(['/dashboard']);
      },
      error: () => {
        console.log('login error');
        this.error = 'Login failed. Please check your email and password.';
      },
    });
  }
}