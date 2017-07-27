import {Component, OnInit} from '@angular/core';

import {BROWSER} from './../../shared/browsers-list'


@Component({
    selector: 'browser-list',
    templateUrl: 'browsers-list.component.html',
})


export class BrowserList implements OnInit {

    browser = BROWSER;
    checked: boolean = false;
    selectedLinks3: string[] = [];

    ngOnInit() {
    }


}



