import { Component, OnInit } from '@angular/core';
import { Video } from '../../models/video';
import { VideoService } from '../../services/video.service';
import { UserService } from '../../services/user.service';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
@Component({
  selector: 'app-video',
  templateUrl: './video.component.html',
  styleUrls: ['./video.component.css'],
  providers: [VideoService, UserService]
})
export class VideoComponent implements OnInit {


  public identity;
  public token;
  public video: Video;
  constructor(
    private _videoService: VideoService,
    private _route: ActivatedRoute,
    private _router: Router,
    private _userService: UserService,
    private _sanitizer: DomSanitizer

  ) {
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.video = new Video(1, this.identity.sub, '', '', '', '', '', '');
    
  }

  ngOnInit(): void {
    this.getVideo();
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
  getVideoIframe(url) {
    var video, results;
 
    if (url === null) {
        return '';
    }
    results = url.match('[\\?&]v=([^&#]*)');
    video   = (results === null) ? url : results[1];
 
    return this._sanitizer.bypassSecurityTrustResourceUrl('https://www.youtube.com/embed/' + video);   
}

}
