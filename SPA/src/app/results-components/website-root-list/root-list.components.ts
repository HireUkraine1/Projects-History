import {Component, OnInit} from '@angular/core';
import {Http} from '@angular/http';

import {DataService} from './../../hero.service';

import {User} from './../user';


@Component({
    selector: 'root-list',
    templateUrl: 'root-list.components.html',
})

export class RootListComponent implements OnInit {

    users: User[];
    selectedLinks: string[] = [];
    checked: boolean = false;

    constructor(private service: DataService) {
    }

    ngOnInit() {
        this.service.getAllUsers()
            .subscribe(users => this.users = users);
    };


}

