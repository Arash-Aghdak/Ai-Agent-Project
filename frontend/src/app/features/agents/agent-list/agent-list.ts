import { Component, OnInit } from '@angular/core';
import { AgentService } from '../../../core/services/agent';
import { CommonModule } from '@angular/common';
@Component({
  selector: 'app-agent-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './agent-list.html',
 
})
export class AgentList implements OnInit {
  agents: any[] = [];

  constructor(private agentService: AgentService) {}

  ngOnInit(): void {
    this.agentService.getAgents().subscribe((data) => {
      this.agents = data;
      console.log(data);
    });
  }
}