import { Component, OnInit, DoCheck } from '@angular/core';
import {UserService} from './services/user.service';
import {Router,ActivatedRoute, Params} from '@angular/router';
 
@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css'],
  providers:[UserService]
})
export class AppComponent implements OnInit, DoCheck{
  public page_title = 'videos-angular';
  public identity;
  public token;


  constructor(
    private _userService:UserService,
    private _router:Router
  ){

  }

  ngDoCheck(){
    this.loadUser();
  }

  ngOnInit(){
  
  }

  loadUser(){
    this.token=this._userService.getToken();
    this.identity=this._userService.getIdentity();
  }
  
  logOut(){

    localStorage.clear();
 
    this.identity=null;
    this.token=null;

   

   }
  
}
