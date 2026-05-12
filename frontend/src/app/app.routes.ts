import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth-guard';
import { loginGuard } from './core/guards/login-guard';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },

  {
    path: 'login',
    canActivate: [loginGuard],
    loadComponent: () =>
      import('./features/auth/login/login').then(m => m.Login),
  },
  {
    path: 'dashboard',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./features/dashboard/home/home').then(m => m.Home),
  },
  {
    path: 'agents/:id/edit',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./features/agents/agent-form/agent-form').then(m => m.AgentForm),
  },
  {
    path: 'agents/create',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./features/agents/agent-form/agent-form').then(m => m.AgentForm),
  },
  {
    path: 'agents',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./features/agents/agent-list/agent-list').then(m => m.AgentList),
  },
  {
    path: 'agents/:agentId/runs/:runId',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./features/task-runs/run-detail/run-detail').then(m => m.RunDetail),
  },
  {
    path: 'agents/:id/history',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./features/task-runs/run-history/run-history').then(m => m.RunHistory),
  },
  {
    path: 'agents/:id/runs',
    canActivate: [authGuard],
    loadComponent: () =>
      import('./features/task-runs/run-list/run-list').then(m => m.RunList),
  },
  {
    path: '**',
    redirectTo: 'login',
  },
];