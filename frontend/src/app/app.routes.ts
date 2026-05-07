import { Routes } from '@angular/router';

export const routes: Routes = [
  { path: '', redirectTo: 'dashboard', pathMatch: 'full' },

  {
    path: 'login',
    loadComponent: () =>
      import('./features/auth/login/login').then(m => m.Login),
  },
  {
    path: 'dashboard',
    loadComponent: () =>
      import('./features/dashboard/home/home').then(m => m.Home),
  },
  {
    path: 'agents/create',
    loadComponent: () =>
      import('./features/agents/agent-form/agent-form').then(m => m.AgentForm),
  },
  {
    path: 'agents',
    loadComponent: () =>
      import('./features/agents/agent-list/agent-list').then(m => m.AgentList),
  },
  {
    path: 'agents/:agentId/runs/:runId',
    loadComponent: () =>
      import('./features/task-runs/run-detail/run-detail').then(m => m.RunDetail),
  },
  {
    path: 'agents/:id/history',
    loadComponent: () =>
      import('./features/task-runs/run-history/run-history').then(m => m.RunHistory),
  },
  {
    path: 'agents/:id/runs',
    loadComponent: () =>
      import('./features/task-runs/run-list/run-list').then(m => m.RunList),
  },
  {
    path: 'runs',
    loadComponent: () =>
      import('./features/task-runs/run-list/run-list').then(m => m.RunList),
  },
];