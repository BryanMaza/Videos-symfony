import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  providers: [
    UserService
  ]
})
export class RegisterComponent implements OnInit {

  public page_title: string;
  public user: User;
  public status;
  public user_edit:boolean;


  constructor(
    public _userService: UserService
  ) {
    this.page_title = "Register";
    this.user = new User(1, '', '', '', '', 'ROLE_USER', '');
    this.user_edit=false;
  }

  ngOnInit(): void {
  }
  onSubmit(form) {

    this._userService.register(this.user).subscribe(
      response=>{
        if(response.status=="success"){
          this.status="success";
          form.reset();
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
