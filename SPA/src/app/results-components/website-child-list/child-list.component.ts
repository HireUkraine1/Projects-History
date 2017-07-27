import {Component, OnInit} from '@angular/core';
import {Http} from '@angular/http';
import {ActivatedRoute} from "@angular/router";

import {DataService} from './../../hero.service';
import {User} from './../user';


@Component({
    selector: 'child-list',
    templateUrl: 'child-list.component.html',
})

export class ChildListComponent implements OnInit {

    user: User[]

    selectedLinks2: string[] = [];
    checked: boolean = false;

    constructor(private service: DataService, private route: ActivatedRoute) {
    }

    ngOnInit() {

        //  grab id from the url
        let id = this.route.snapshot.params['id'];

        // use the servise to getUsers()
        this.reloadRoot(id)
    };


    // reload child users
    reloadRoot(id: number) {
        this.service.getChildUser(id)
            .subscribe(user => this.user = user);
    }

}

