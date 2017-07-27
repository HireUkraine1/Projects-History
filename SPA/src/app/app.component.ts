import {Component} from '@angular/core';


require('./app.components.global.sass');


@Component({
    selector: 'my-app',
    templateUrl: 'app.components.html',
    styles: [String(require('./app.components.sass'))],
})


export class AppComponent {
    title = 'parent components';
}   