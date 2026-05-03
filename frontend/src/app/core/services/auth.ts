import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { tap } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AuthService {
  private apiUrl = 'http://127.0.0.1:8000/api/login';
  private tokenKey = 'mother_agent_token';

  constructor(private http: HttpClient) {}

  login(email: string, password: string) {
    return this.http.post<{ token: string }>(this.apiUrl, { email, password }).pipe(
      tap((response) => {
        localStorage.setItem(this.tokenKey, response.token);
      })
    );
  }

  getToken(): string | null {
    return localStorage.getItem(this.tokenKey);
  }

  logout(): void {
    localStorage.removeItem(this.tokenKey);
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}