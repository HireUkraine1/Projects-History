import {ModuleWithProviders} from '@angular/core';
import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';


import {HomeComponent} from './home-components/home.components';
import {ResultComponets} from './results-components/result.component';

import {ChildListComponent} from "./results-components/website-child-list/child-list.component";
import {RootListComponent} from "./results-components/website-root-list/root-list.components";


export const routes: Routes = [
    {
        path: '',
        component: HomeComponent
    },
    {
        path: 'results',
        component: ResultComponets,
        children: [
            {
                path: '',
                component: RootListComponent

            },
            {
                path: ':id',
                component: ChildListComponent
            },
        ]
    }

];


export const routing: ModuleWithProviders = RouterModule.forRoot(routes, {useHash: false});