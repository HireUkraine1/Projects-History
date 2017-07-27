import {Component, Input} from '@angular/core';
import {
    trigger,
    state,
    style,
    animate,
    transition,
    keyframes
} from '@angular/animations';

import {ScrollimateService} from "ng2-scrollimate";

import {Icons} from './icons';
import {ICONS} from './home-icons-list';
import {Observable} from "rxjs/Observable";

declare var jQuery: any;
declare var $: any;


@Component({
    selector: 'icons-detail',
    template: ` 
              <div *ngFor=" let icon of iconlist"
              [@elementState]="scrollimateOptions.myScrollimation.currentState" [scrollimate]="scrollimateOptions.myScrollimation"
              class="col-md-3 col-xs-4 features-items">
               <div class="features-ico">
                  <img src="{{icon.url}}" alt="features">
               </div>
               <h3>{{icon.title}}</h3>          
              </div>
    `,
    animations: [
        trigger('elementState', [
            state('inactive', style({
                opacity: '0',
                transform: "translateY(100px)"
            })),
            state('active', style({
                opacity: '1',
                transform: "translateY(0px)"

            })),
            transition('inactive => active', animate(' 200ms ease-in')),
            transition('active => inactive', animate(' 200ms ease-out'))
        ])
    ]
})


export class IconsComponent {
    iconlist: Icons[] = ICONS;
    scrollimateOptions: any = {
        myScrollimation: {
            currentState: "inactive",
            states: [
                {
                    method: "pxElement",
                    value: 300,
                    state: "active",
                },
                {
                    method: "default",
                    state: "inactive"
                }
            ]
        },
    }

    ngAfterViewInit() {
        $(document).ready(function () {
            $(".features-items").click(function () {
                console.log('123');
            });
        });
    }

}