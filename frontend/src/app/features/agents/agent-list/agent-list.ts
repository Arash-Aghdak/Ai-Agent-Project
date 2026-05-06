import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
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
  agents: any[] = [];
  isLoading = true;
  errorMessage = '';
  selectedAgent: any = null;
  inputText = '';
  isRunning = false;
  runErrorMessage = '';

  constructor(
    private agentService: AgentService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.agentService.getAgents().subscribe({
      next: (data) => {
        this.agents = [...data];
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

    this.isRunning = true;
    this.runErrorMessage = '';

    this.agentService.runAgent(this.selectedAgent.id, this.inputText).subscribe({
      next: (result) => {
        console.log('task run created:', result);
        this.isRunning = false;
        this.selectedAgent = null;
        this.inputText = '';
      },
      error: (err) => {
        this.isRunning = false;
        this.runErrorMessage = err?.error?.error || 'Task could not be started.';
        console.log('run task error:', err);
      },
    });
  }
}