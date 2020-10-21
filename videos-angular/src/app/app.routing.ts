import { Routes,RouterModule } from '@angular/router';
import {ModuleWithProviders} from '@angular/core';

import {LoginComponent} from './components/login/login.component';
import {HomeComponent} from './components/home/home.component';
import {RegisterComponent} from './components/register/register.component';
import {ErrorComponent} from './components/error/error.component';
import {UserEditComponent} from './components/user-edit/user-edit.component';
import {NewVideoComponent} from './components/new-video/new-video.component';
import {VideoEditComponent} from './components/video-edit/video-edit.component';
import {VideoComponent} from './components/video/video.component';
import {IdentityGuard} from './services/identity.guard';


 const appRoutes: Routes = [
    {path:'',component:HomeComponent},
    {path:'inicio',component:HomeComponent},
    {path:'inicio/:page',component:HomeComponent},
    {path:'login',component:LoginComponent},
    {path:'salir/:sure',component:LoginComponent},
    {path:'registro',canActivate:[IdentityGuard],component:RegisterComponent},
    {path:'ajustes', canActivate:[IdentityGuard],component:UserEditComponent},
    {path:'guardar-favorito',canActivate:[IdentityGuard],component:NewVideoComponent},
    {path:'editar/:id',component:VideoEditComponent},
    {path:'video/:id',component:VideoComponent},
    {path:'error',component:ErrorComponent},
    {path:'**',component:ErrorComponent},
    
 ];

 export const appRoutingProviders:any []=[];

 export const routing:ModuleWithProviders<any>=RouterModule.forRoot(appRoutes);
