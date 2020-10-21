import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { Video } from '../../models/video';
import { VideoService } from '../../services/video.service';
@Component({
  selector: 'app-video-edit',
  templateUrl: '../new-video/new-video.component.html',
  styleUrls: ['./video-edit.component.css'],
  providers: [UserService, VideoService]
})
export class VideoEditComponent implements OnInit {

  public identity;
  public token;
  public page_title: string;
  public video: Video;
  public status;
  public video_edit: boolean;
  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute,
    private _videoService: VideoService
  ) {
    this.page_title = "Editar video favorito";
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.video = new Video(1, this.identity.sub, '', '', '', '', '', '');
    this.video_edit = true;
  }
  ngOnInit(): void {
    this.getVideo();
  }

  onSubmit(form) {

    this._videoService.edit(this.video,this.token,this.video.id).subscribe(
      response=>{
        if(response.status=="success"){
          this.video=response.video;
          this.status="success";
          this._router.navigate(['/inicio']);
        }else{

          this.status="error";
        }
        console.log(response);
        
      },
      error=>{
        this.status="error";
        console.log(error);
        
      }
    );
  }

  getVideo() {
    let id;
    this._route.params.subscribe(
      params => {
        id = +params['id'];
        this._videoService.getVideo(this.token, id).subscribe(
          response => {
            if (response.status == "success") {
              this.video = response.video;
            }
          },
          error => {
            console.log(error);

          }
        );
      }
    );

  }

}
