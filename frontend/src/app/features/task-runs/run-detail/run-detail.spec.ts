import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RunDetail } from './run-detail';

describe('RunDetail', () => {
  let component: RunDetail;
  let fixture: ComponentFixture<RunDetail>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RunDetail]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RunDetail);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
