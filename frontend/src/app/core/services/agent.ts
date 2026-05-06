import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

export interface CreateAgentPayload {
  name: string;
  role: string;
  instructions: string;
  model: string;
}

@Injectable({
  providedIn: 'root',
})
export class AgentService {
  private apiUrl = 'http://127.0.0.1:8000/api/agents';

  constructor(private http: HttpClient) {}

  getAgents(): Observable<any[]> {
    return this.http.get<any[]>(this.apiUrl);
  }

  createAgent(payload: CreateAgentPayload): Observable<any> {
    return this.http.post<any>(this.apiUrl, payload);
  }

  runAgent(agentId: number, inputText: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${agentId}/run`, {
      inputText,
    });
  }

  getAgentRuns(agentId: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/${agentId}/runs`);
  }
}