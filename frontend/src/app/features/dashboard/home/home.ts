import { Component, OnInit } from '@angular/core';
import { AgentService } from '../../../core/services/agent';

@Component({
  selector: 'app-home',
  imports: [],
  templateUrl: './home.html',
  styleUrl: './home.css',
})
export class Home implements OnInit {
  agentCount = 0;
  taskRunCount = 0;

  constructor(private agentService: AgentService) {}

  ngOnInit(): void {
    this.agentService.getAgents().subscribe({
      next: (agents) => {
        this.agentCount = agents.length;
      },
      error: (err) => {
        console.log('dashboard agents error:', err);
      },
    });
  }
}