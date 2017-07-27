import {Component, OnInit} from '@angular/core';
import {Http} from '@angular/http';
import {User} from './user';
import {DataService} from './../hero.service';


@Component({
    selector: 'result-app',
    templateUrl: 'result.component.html',
    styles: [String(require('./result.component.sass'))],

})

export class ResultComponets implements OnInit {

    title = 'our table'
    webUrl: string
    users: User[];
    IsHidden = true;

    constructor(private service: DataService) {
    }

    ngOnInit() {
        this.pathRequest();

        this.service.getAllUsers()
            .subscribe(users => this.users = users);
    };

    pathRequest() {
        this.webUrl = this.service.getRequest();
    }

    showBrowsers() {
        this.IsHidden = !this.IsHidden;
    }

    showTab() {
        this.IsHidden = !this.IsHidden;
    }


}

