import {Component, OnInit} from '@angular/core';
import {Http} from '@angular/http';
import {Router} from '@angular/router';

import {DataService} from './../hero.service';


@Component({
    selector: 'header-comp',
    templateUrl: './header.components.html',
    styles: [String(require('./header.components.sass'))],

})

export class HeaderComponent {

    constructor(private service: DataService, private router: Router) {

    }

    addTodo(title: string) {
        this.service.pathRequest(title)
        this.router.navigate(['/results']);
    }

}