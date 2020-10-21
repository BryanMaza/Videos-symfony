import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { global } from './global';
import { UserService } from './user.service';
@Injectable()
export class VideoService {
    public url;

    

    constructor(
        private _http:HttpClient
    ) {

        this.url=global.url;

     }

     newVideo(video,token):Observable<any>{
        let json=JSON.stringify(video);
        let params="json="+json;
        let headers= new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded').set('Authorization',token);

        return this._http.post(this.url+"video/new",params,{headers:headers});

     }
     getVideos(token,page):Observable<any>{

        if(!page){
            page=1;
        }

        let headers=new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded').set('Authorization',token);
        return this._http.get(this.url+"video/list?page="+page,{headers:headers});
     }
     delete(id,token):Observable<any>{
        let headers=new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded').set('Authorization',token);
        return this._http.delete(this.url+"video/remove/"+id,{headers:headers});
     }
     getVideo(token,id):Observable<any>{
      let headers=new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded').set('Authorization',token);
      return this._http.get(this.url+"video/detail/"+id,{headers:headers});
     }
     edit(video,token,id):Observable<any>{
      let json=JSON.stringify(video);
      let params="json="+json;
      let headers=new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded').set('Authorization',token);

      return this._http.put(this.url+"video/edit/"+id,params,{headers:headers});


     }
    
     


}