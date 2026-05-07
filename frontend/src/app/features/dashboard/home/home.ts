import { CommonModule } from '@angular/common';
import { Component, OnInit, computed, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { forkJoin } from 'rxjs';
import { AgentService } from '../../../core/services/agent';

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit {
  agents = signal<any[]>([]);
  runs = signal<any[]>([]);

  isLoading = signal(true);
  errorMessage = signal('');

  agentCount = computed(() => this.agents().length);
  runCount = computed(() => this.runs().length);

  completedCount = computed(() =>
    this.runs().filter((run) => run.status === 'completed').length
  );

  failedCount = computed(() =>
    this.runs().filter((run) => run.status === 'failed').length
  );

  latestRuns = computed(() => this.runs().slice(0, 5));

  constructor(private agentService: AgentService) {}

  ngOnInit(): void {
    this.loadDashboard();
  }

  loadDashboard(): void {
    this.isLoading.set(true);
    this.errorMessage.set('');

    this.agentService.getAgents().subscribe({
      next: (agents) => {
        this.agents.set(agents);

        if (agents.length === 0) {
          this.runs.set([]);
          this.isLoading.set(false);
          return;
        }

        const runRequests = agents.map((agent) =>
          this.agentService.getAgentRuns(agent.id)
        );

        forkJoin(runRequests).subscribe({
          next: (runsByAgent) => {
            const allRuns = runsByAgent
              .flat()
              .sort((a, b) =>
                new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime()
              );

            this.runs.set(allRuns);
            this.isLoading.set(false);
          },
          error: (err) => {
            this.isLoading.set(false);
            this.errorMessage.set('Task run statistics could not be loaded.');
            console.log('dashboard runs error:', err);
          },
        });
      },
      error: (err) => {
        this.isLoading.set(false);
        this.errorMessage.set('Dashboard data could not be loaded.');
        console.log('dashboard agents error:', err);
      },
    });
  }
}