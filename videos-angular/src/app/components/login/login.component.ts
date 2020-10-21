import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import {Router,ActivatedRoute,Params} from '@angular/router'

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers:[UserService]
})
export class LoginComponent implements OnInit {

  
  public page_title: string;
  public user: User;
  public status;
  public identity;
  public token;
  

  constructor(
    private _userService:UserService,
    private _router:Router,
    private _route:ActivatedRoute
  ) {
    this.page_title="Login";
    this.user = new User(1, '', '', '', '', 'ROLE_USER', '');
    this.status="";
   }

  ngOnInit(): void {

   
  }

  onSubmit(form){
    this._userService.login(this.user).subscribe(
      response=>{
        if(response){
          if(response.status=="error"){
            this.status="error"
          }else{
           
            this.status="success";
            this.identity=response;
           
           
            this._userService.login(this.user,true).subscribe(
              response=>{
                this.token=response;
                localStorage.setItem("identity",JSON.stringify( this.identity));
                localStorage.setItem("token",this.token);
               
                this._router.navigate(['/inicio']);
                
              },
              error=>{
                console.log(error);
                
              }
            );
         
          }
        
        }
        
      },
      error=>{
        this.status="error"
        console.log(error);
        
      }
    );
  }

  

}
