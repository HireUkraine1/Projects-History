import {NgModule} from '@angular/core';
import {CommonModule, APP_BASE_HREF, LocationStrategy, HashLocationStrategy} from '@angular/common';
import {BrowserModule} from '@angular/platform-browser';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {HttpModule} from '@angular/http';
import {FormsModule} from '@angular/forms';

import {
    ChartModule,
    SharedModule,
    AccordionModule,
    CheckboxModule,
    ButtonModule
} from 'primeng/primeng';

import {
    MaterialModule,
    MdProgressBarModule,
    MdCheckboxModule
} from '@angular/material';

import {Ng2ScrollimateModule} from 'ng2-scrollimate';

import {AppComponent} from './app.component';
import {routing} from './app-routing.module';
import {HeaderComponent} from './header-components/header.components';
import {TopLineComponent} from './header-components/header-top-line/top-line.components';
import {HomeComponent} from './home-components/home.components';
import {IconsComponent} from './home-components/home-components-icons/home-icons.components';
import {FooterComponent} from './footer-components/footer.component';
import {ResultComponets} from './results-components/result.component';
import {NotFoundComponent} from './404-components/not-found.component';
import {BrowserList} from './results-components/browsers-list/browsers-list.componet';
import {RootListComponent} from './results-components/website-root-list/root-list.components';
import {ChildListComponent} from './results-components/website-child-list/child-list.component';

import {DataService} from './hero.service';

import 'hammerjs';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import 'rxjs/add/observable/throw';

@NgModule({
    imports: [
        BrowserModule,
        BrowserAnimationsModule,
        Ng2ScrollimateModule,
        FormsModule,
        routing,
        HttpModule,
        MaterialModule,
        MdProgressBarModule,
        MdCheckboxModule,
        ChartModule,
        SharedModule,
        AccordionModule,
        CheckboxModule,
        ButtonModule
    ],
    declarations: [
        AppComponent,
        HomeComponent,
        HeaderComponent,
        TopLineComponent,
        IconsComponent,
        FooterComponent,
        ResultComponets,
        NotFoundComponent,
        BrowserList,
        RootListComponent,
        ChildListComponent
    ],
    providers: [
        DataService,
        {provide: APP_BASE_HREF, useValue: '/'},
        {provide: LocationStrategy, useClass: HashLocationStrategy}
    ],
    bootstrap: [
        AppComponent
    ]
})

export class AppModule {
}

 