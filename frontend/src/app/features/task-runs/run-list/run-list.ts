import { CommonModule } from '@angular/common';
import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { AgentService } from '../../../core/services/agent';

@Component({
  selector: 'app-run-list',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './run-list.html',
  styleUrl: './run-list.css',
})
export class RunList implements OnInit {
  agentId!: number;
  runs: any[] = [];

  isLoading = true;
  errorMessage = '';

  constructor(
    private route: ActivatedRoute,
    private agentService: AgentService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit(): void {
    this.agentId = Number(this.route.snapshot.paramMap.get('id'));
    this.loadRuns();
  }

  loadRuns(): void {
    this.isLoading = true;
    this.errorMessage = '';

    this.agentService.getAgentRuns(this.agentId).subscribe({
      next: (data) => {
        this.runs = data;
        this.isLoading = false;

        console.log('runs:', this.runs);

        this.cdr.detectChanges();
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = 'Task runs could not be loaded.';

        console.log('runs error:', err);

        this.cdr.detectChanges();
      },
    });
  }
}