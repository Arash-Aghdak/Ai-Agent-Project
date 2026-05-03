import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class AgentService {
  private apiUrl = 'http://127.0.0.1:8000/api/agents';

  constructor(private http: HttpClient) {}

  getAgents(): Observable<any> {
    return this.http.get(this.apiUrl);
  }
}