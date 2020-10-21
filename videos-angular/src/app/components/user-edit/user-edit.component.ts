import { Component, OnInit } from '@angular/core';
import {User} from '../../models/user';
import {UserService} from '../../services/user.service';
@Component({
  selector: 'app-user-edit',
  templateUrl: '../register/register.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers:[UserService]
})
export class UserEditComponent implements OnInit {

  public page_title:string;
  public user_edit:boolean;
  public user:User;
  public status;
  public token;
  public identity;
  constructor(
    public _userService:UserService
  ) { 
    this.page_title="Ajustes";
    this.user_edit=true;
   
    this.token=this._userService.getToken();
    this.identity=this._userService.getIdentity();
    //this.user= new User(this.identity.sub,this.identity.name,this.identity.surname,this.identity.email,this.identity.password,this.identity.role,this.identity.created_at);
    this.user=this.identity;
  }

  ngOnInit(): void {
  
  }
  onSubmit(form){

    
    
    this._userService.update(this.user).subscribe(
      response=>{
        if(response.status=="success"){
          
          this.status="success";
          localStorage.setItem('identity',JSON.stringify( response.user));
        }else{
          this.status="error";
        } 

      },
      error=>{
        this.status="error";
        console.log(error);
        
      }
    );
  }


}
