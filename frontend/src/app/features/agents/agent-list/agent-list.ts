import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { AgentService } from '../../../core/services/agent';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-agent-list',
  standalone: true,
  imports: [CommonModule, RouterLink, FormsModule],
  templateUrl: './agent-list.html',
  styleUrl: './agent-list.css',
})
export class AgentList implements OnInit {
  agents= signal<any[]>([]);
  isLoading = true;
  errorMessage = signal('');
  selectedAgent: any = null;
  inputText = '';
  isRunning = false;
  runErrorMessage = '';

  constructor(
    private agentService: AgentService,
    private cdr: ChangeDetectorRef,
    private router: Router,
  ) {}

  ngOnInit(): void {
    this.agentService.getAgents().subscribe({
      next: (data) => {
        this.agents.set(data);
        this.isLoading = false;
        console.log('agents:', this.agents);

        this.cdr.detectChanges();
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage.set('Agents could not be loaded.');
        console.log('agents error:', err);

        this.cdr.detectChanges();
      },
    });
  }

  openRunForm(agent: any): void {
    this.selectedAgent = agent;
    this.inputText = '';
    this.runErrorMessage = '';
  }

  cancelRun(): void {
    this.selectedAgent = null;
    this.inputText = '';
    this.runErrorMessage = '';
  }

  runTask(): void {
    if (!this.selectedAgent || !this.inputText.trim()) {
      this.runErrorMessage = 'Please enter a task.';
      return;
    }

    if (this.inputText.trim().length < 5) {
      this.runErrorMessage = 'Task input must be at least 5 characters long.';
      return;
    }

    const agentId = this.selectedAgent.id;

    this.isRunning = true;
    this.runErrorMessage = '';

    this.agentService.runAgent(agentId, this.inputText).subscribe({
      next: (result) => {
        console.log('task run created:', result);

        this.isRunning = false;
        this.selectedAgent = null;
        this.inputText = '';

        const taskRunId = result?.data?.taskRunId;

        if (taskRunId) {
          this.router.navigate(['/agents', agentId, 'runs', taskRunId]);
        } else {
          this.router.navigate(['/agents', agentId, 'runs']);
        }
      },
      error: (err) => {
        this.isRunning = false;
        this.runErrorMessage = this.getErrorMessage(err, 'Task could not be started.');
        console.log('run task error:', err);
      },
    });
  }

  deleteAgent(agent: any): void {
    this.errorMessage.set('');

    const confirmed = confirm(
      `Delete agent "${agent.name}"?`
    );

    if (!confirmed) {
      return;
    }

    this.agentService.deleteAgent(agent.id).subscribe({
      next: () => {
        this.agents.update((agents) =>
          agents.filter((item) => item.id !== agent.id)
        );

        console.log('agent deleted');
      },
      error: (err) => {
        this.errorMessage.set(
          this.getErrorMessage(
            err,
            'Agent could not be deleted. Please check if the backend is running.'
          )
        );

        console.log('delete error:', err);
      },
    });
  }

  private getErrorMessage(err: any, fallback: string): string {
    if (err?.status === 0) {
      return 'Backend is not reachable. Please make sure Symfony is running.';
    }

    if (err?.error?.errors && Array.isArray(err.error.errors)) {
      return err.error.errors
        .map((item: any) => item.message)
        .join(' ');
    }

    if (err?.error?.error) {
      return err.error.error;
    }

    if (err?.error?.message) {
      return err.error.message;
    }

    return fallback;
  }
}