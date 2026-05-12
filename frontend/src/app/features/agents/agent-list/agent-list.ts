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
  errorMessage = '';
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
        this.errorMessage = 'Agents could not be loaded.';
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

    const agentId = this.selectedAgent.id;

    this.isRunning = true;
    this.runErrorMessage = '';

    this.agentService.runAgent(agentId, this.inputText).subscribe({
      next: (result) => {
        console.log('task run created:', result);

        this.isRunning = false;
        this.selectedAgent = null;
        this.inputText = '';

        if (result?.taskRunId) {
          this.router.navigate(['/agents', agentId, 'runs', result.taskRunId]);
        } else {
          this.router.navigate(['/agents', agentId, 'runs']);
        }
      },
      error: (err) => {
        this.isRunning = false;
        this.runErrorMessage = err?.error?.error || 'Task could not be started.';
        console.log('run task error:', err);
      },
    });
  }

  deleteAgent(agent: any): void {
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
        console.log('delete error:', err);
      },
    });
  }
}