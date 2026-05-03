import { Component } from '@angular/core';
import { MatToolbarModule } from '@angular/material/toolbar';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-layout',
  standalone: true,
  imports: [MatToolbarModule, RouterModule],
  templateUrl: './layout.html',
  styleUrl: './layout.css',
})
export class Layout {

}
