import {Injectable} from '@angular/core';
import {Http, Headers} from '@angular/http';
import {Observable} from "rxjs/Observable";
import {Subject} from 'rxjs/Subject';

import 'rxjs/add/operator/map';

import {User} from './results-components/user'


@Injectable()
export class DataService {

    data: string
    private webSiteUrl: string = 'https://reqres.in/api/users?page=';

    constructor(private http: Http) {
    }

    getRequest() {
        return this.data
    }

    pathRequest(data: string) {
        this.data = data
    }

    /**
     * get all users
     */
    getAllUsers(): Observable<User[]> {
        return this.http.get(this.webSiteUrl + '2')
            .map(res => res.json())

    }

    /**
     * get child users
     */
    getChildUser(id: number): Observable<User[]> {
        return this.http.get(`${this.webSiteUrl + id}`)
            .map(res => res.json());
    }


}
