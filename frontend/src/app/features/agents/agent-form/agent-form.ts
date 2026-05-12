import { CommonModule } from '@angular/common';
import { Component, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { AgentService, CreateAgentPayload } from '../../../core/services/agent';

@Component({
  selector: 'app-agent-form',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink],
  templateUrl: './agent-form.html',
  styleUrl: './agent-form.css',
})
export class AgentForm implements OnInit {
  isLoading = signal(false);
  isSaving = signal(false);
  errorMessage = signal('');

  isEditMode = false;
  agentId: number | null = null;

  agent: CreateAgentPayload & { isActive?: boolean } = {
    name: '',
    role: '',
    instructions: '',
    model: 'gpt-4o-mini',
    isActive: true,
  };

  constructor(
    private agentService: AgentService,
    private router: Router,
    private route: ActivatedRoute
  ) {}

  ngOnInit(): void {
    const idParam = this.route.snapshot.paramMap.get('id');

    if (idParam) {
      this.isEditMode = true;
      this.agentId = Number(idParam);
      this.loadAgent(this.agentId);
    }
  }

  loadAgent(id: number): void {
    this.isLoading.set(true);
    this.errorMessage.set('');

    this.agentService.getAgent(id).subscribe({
      next: (agent) => {
        this.agent = {
          name: agent.name,
          role: agent.role,
          instructions: agent.instructions,
          model: agent.model,
          isActive: agent.isActive,
        };

        this.isLoading.set(false);
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set('Agent could not be loaded.');
        console.log('load agent error:', err);
      },
    });
  }

  onSubmit(): void {
    this.errorMessage.set('');

    if (!this.agent.name.trim() || !this.agent.role.trim() || !this.agent.instructions.trim()) {
      this.errorMessage.set('Please fill in all required fields.');
      return;
    }

    this.isSaving.set(true);

    if (this.isEditMode && this.agentId) {
      this.agentService.updateAgent(this.agentId, this.agent).subscribe({
        next: () => {
          this.isSaving.set(false);
          this.router.navigate(['/agents']);
        },
        error: (err) => {
          this.isSaving.set(false);
          this.errorMessage.set(err?.error?.error || 'Agent could not be updated.');
          console.log('update agent error:', err);
        },
      });

      return;
    }

    this.agentService.createAgent(this.agent).subscribe({
      next: () => {
        this.isSaving.set(false);
        this.router.navigate(['/agents']);
      },
      error: (err) => {
        this.isSaving.set(false);
        this.errorMessage.set(err?.error?.error || 'Agent could not be created.');
        console.log('create agent error:', err);
      },
    });
  }
}