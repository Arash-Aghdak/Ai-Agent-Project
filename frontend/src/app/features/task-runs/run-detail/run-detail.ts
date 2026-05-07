import { CommonModule } from '@angular/common';
import { Component, OnInit, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { AgentService } from '../../../core/services/agent';
import { MarkdownModule } from 'ngx-markdown';

@Component({
  selector: 'app-run-detail',
  standalone: true,
  imports: [CommonModule, RouterLink, MarkdownModule],
  templateUrl: './run-detail.html',
  styleUrl: './run-detail.css',
})
export class RunDetail implements OnInit {
  agentId!: number;
  runId!: number;

  run = signal<any | null>(null);
  isLoading = signal(true);
  errorMessage = signal('');

  constructor(
    private route: ActivatedRoute,
    private agentService: AgentService
  ) {}

  ngOnInit(): void {
    this.agentId = Number(this.route.snapshot.paramMap.get('agentId'));
    this.runId = Number(this.route.snapshot.paramMap.get('runId'));

    this.loadRun();
  }

  loadRun(): void {
    this.isLoading.set(true);
    this.errorMessage.set('');

    this.agentService.getAgentRuns(this.agentId).subscribe({
      next: (runs) => {
        const foundRun = runs.find((item) => item.id === this.runId) || null;

        this.run.set(foundRun);
        this.isLoading.set(false);

        if (!foundRun) {
          this.errorMessage.set('Task run not found.');
        }

        console.log('run detail:', foundRun);
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set('Task run could not be loaded.');
        console.log('run detail error:', err);
      },
    });
  }
}