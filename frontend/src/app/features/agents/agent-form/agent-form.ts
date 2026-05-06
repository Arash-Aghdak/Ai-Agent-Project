import { CommonModule } from '@angular/common';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { AgentService, CreateAgentPayload } from '../../../core/services/agent';

@Component({
  selector: 'app-agent-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './agent-form.html',
  styleUrl: './agent-form.css',
})
export class AgentForm {
  isLoading = false;
  errorMessage = '';

  agent: CreateAgentPayload = {
    name: '',
    role: '',
    instructions: '',
    model: 'gpt-4o-mini',
  };

  constructor(
    private agentService: AgentService,
    private router: Router
  ) {}

  onSubmit(): void {
    this.errorMessage = '';

    if (!this.agent.name.trim() || !this.agent.role.trim() || !this.agent.instructions.trim()) {
      this.errorMessage = 'Please fill in all required fields.';
      return;
    }

    this.isLoading = true;

    this.agentService.createAgent(this.agent).subscribe({
      next: () => {
        this.isLoading = false;
        this.router.navigate(['/agents']);
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = err?.error?.message || 'Agent could not be created.';
        console.log('create agent error:', err);
      },
    });
  }
}