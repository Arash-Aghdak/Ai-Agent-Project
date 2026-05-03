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
    path: 'agents',
    loadComponent: () =>
      import('./features/agents/agent-list/agent-list').then(m => m.AgentList),
  },
  {
    path: 'runs',
    loadComponent: () =>
      import('./features/task-runs/run-list/run-list').then(m => m.RunList),
  },
];