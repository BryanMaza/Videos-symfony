import { Component, OnInit } from '@angular/core';
import {Router,ActivatedRoute,Params} from '@angular/router';
import{UserService} from '../../services/user.service';
import {Video} from '../../models/video';
import {VideoService} from '../../services/video.service';
@Component({
  selector: 'app-new-video',
  templateUrl: './new-video.component.html',
  styleUrls: ['./new-video.component.css'],
  providers:[UserService, VideoService]
})
export class NewVideoComponent implements OnInit {

  public identity;
  public token;
  public page_title:string;
  public video:Video;
  public status;
  constructor(
    private _userService:UserService,
    private _router:Router,
    private _route:ActivatedRoute,
    private _videoService:VideoService
  ) { 
    this.page_title="Guardar video favorito";
    this.identity=this._userService.getIdentity();
    this.token=this._userService.getToken();
    this.video=new Video(1,this.identity.sub,'','','','','','');
  }

  ngOnInit(): void {
   
  }
  onSubmit(form){
    this._videoService.newVideo(this.video,this.token).subscribe(
      response=>{
        if(response.status=="success"){
          this.video=response.video;
          this.status="success";
          this._router.navigate(['/inicio']);
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
