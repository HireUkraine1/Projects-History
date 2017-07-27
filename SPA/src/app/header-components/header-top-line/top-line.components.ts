import {
    Component,
    HostListener,
    Inject,
    OnInit
} from '@angular/core';

import {
    trigger,
    state,
    style,
    animate,
    transition
} from '@angular/animations';

import {DOCUMENT} from '@angular/platform-browser';

@Component({
    selector: 'topline-comp',
    templateUrl: './top-line.components.html',
    // styles: [String(require('./home.components.sass'))],
    animations: [

        trigger('mobileMenu', [
            state('inactive', style({
                opacity: '0',
                display: 'none'
            })),
            state('active', style({
                opacity: '1',
                display: 'flex'
            })),
            transition('inactive => active', animate('300ms ease-in')),
            transition('active => inactive', animate('300ms ease-out'))
        ]),

        trigger('mobileMenuItems', [
            state('inactive', style({
                transform: 'scale(0.5)',
                opacity: '0',
                top: '100px',
            })),
            state('active', style({
                transform: 'scale(1)',
                top: '0px',
                opacity: '1',
            })),
            transition('inactive => active', animate('0.1s 400ms ease-in')),
            transition('active => inactive', animate('0.1s 400ms ease-out'))
        ]),

        trigger('mobileMenuItems2', [
            state('inactive', style({
                transform: 'scale(0.5)',
                opacity: '0',
                top: '100px',
            })),
            state('active', style({
                transform: 'scale(1)',
                top: '0px',
                opacity: '1',
            })),
            transition('inactive => active', animate('0.2s 400ms ease-in')),
            transition('active => inactive', animate('0.2s 400ms ease-out'))
        ]),

        trigger('mobileMenuItems3', [
            state('inactive', style({
                transform: 'scale(0.5)',
                opacity: '0',
                top: '100px',
            })),
            state('active', style({
                transform: 'scale(1)',
                top: '0px',
                opacity: '1',
            })),
            transition('inactive => active', animate('0.3s 400ms ease-in')),
            transition('active => inactive', animate('0.3s 400ms ease-out'))
        ]),

        trigger("elementState", [
            state("inactive", style({
                opacity: 0,
                visibility: "hidden",
            })),
            state("active", style({
                opacity: 1,
                visibility: "visible",
            })),
            transition("* => active", animate("200ms ease-in")),
            transition("* => inactive", animate("200ms ease-out")),
        ])
    ]

})

export class TopLineComponent {
    name: string = '';
    isClassVisible: false;
    IsHidden = true;
    isMobNav: false;
    state: string = 'inactive'

    public navIsFixed: boolean = false;
    scrollimateOptions: any = {
        myScrollimation: {
            currentState: "inactive",
            states: [
                {
                    method: "percentTotal",
                    value: 85,
                    state: "active",
                },
                {
                    method: "default",
                    state: "inactive"
                }
            ]
        },
    }

    constructor(@Inject(DOCUMENT) private document: Document) {
    }

    ngOnInit() {
    }

    @HostListener("window:scroll", [])

    onWindowScroll() {
        let number = this.document.body.scrollTop;
        if (number > 50) {
            this.navIsFixed = true;
        } else if (this.navIsFixed && number < 10) {
            this.navIsFixed = false;
        }
    }

    onSelect() {
        this.IsHidden = !this.IsHidden;
        this.state = (this.state === 'inactive' ? 'active' : 'inactive');
    }

}
