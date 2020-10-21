import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import {routing, appRoutingProviders} from './app.routing';
import {FormsModule} from '@angular/forms';
import {HttpClientModule} from '@angular/common/http';

import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { ErrorComponent } from './components/error/error.component';
import { RegisterComponent } from './components/register/register.component';
import { LoginComponent } from './components/login/login.component';
import { UserEditComponent } from './components/user-edit/user-edit.component';
import { NewVideoComponent } from './components/new-video/new-video.component';

import {IdentityGuard} from './services/identity.guard';
import {UserService} from './services/user.service';
import {VideoService} from './services/video.service';
import { VideoEditComponent } from './components/video-edit/video-edit.component';
import { VideoComponent } from './components/video/video.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    ErrorComponent,
    RegisterComponent,
    LoginComponent,
    UserEditComponent,
    NewVideoComponent,
    VideoEditComponent,
    VideoComponent
  ],
  imports: [
    BrowserModule,
    routing,
    FormsModule,
    HttpClientModule
  ],
  providers: [
    appRoutingProviders,
    IdentityGuard,
    UserService,
    VideoService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
