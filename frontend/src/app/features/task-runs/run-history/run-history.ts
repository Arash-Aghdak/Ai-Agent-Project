import { CommonModule } from '@angular/common';
import { Component, OnInit, computed, signal } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { AgentService } from '../../../core/services/agent';

@Component({
  selector: 'app-run-history',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './run-history.html',
  styleUrl: './run-history.css',
})
export class RunHistory implements OnInit {
  agentId!: number;

  agent = signal<any | null>(null);
  runs = signal<any[]>([]);
  isLoading = signal(true);
  errorMessage = signal('');

  totalRuns = computed(() => this.runs().length);

  completedRuns = computed(() =>
    this.runs().filter((run) => run.status === 'completed').length
  );

  failedRuns = computed(() =>
    this.runs().filter((run) => run.status === 'failed').length
  );

  lastRun = computed(() => this.runs()[0] || null);

  constructor(
    private route: ActivatedRoute,
    private agentService: AgentService
  ) {}

  ngOnInit(): void {
    this.agentId = Number(this.route.snapshot.paramMap.get('id'));
    this.loadHistory();
  }

  loadHistory(): void {
    this.isLoading.set(true);
    this.errorMessage.set('');

    this.agentService.getAgents().subscribe({
      next: (agents) => {
        const foundAgent = agents.find((item) => item.id === this.agentId) || null;
        this.agent.set(foundAgent);

        this.agentService.getAgentRuns(this.agentId).subscribe({
          next: (runs) => {
            this.runs.set(runs);
            this.isLoading.set(false);
          },
          error: (err) => {
            this.isLoading.set(false);
            this.errorMessage.set('Run history could not be loaded.');
            console.log('history runs error:', err);
          },
        });
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set('Agent could not be loaded.');
        console.log('history agent error:', err);
      },
    });
  }
}