import { Injectable } from '@angular/core';
import { User } from '../models/user';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { global } from './global';
@Injectable()
export class UserService {
    public url: string;
    public token;
    public identity;
    constructor(
        private _http: HttpClient
    ) {

        this.url = global.url;
    }

    prueba() {
        return "Hola mudnode desde el servicio"
    }

    register(data): Observable<any> {
        let json = JSON.stringify(data);
        let params = "json=" + json;
        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

        return this._http.post(this.url + "register", params, { headers });
    }
    login(data,gettoken=null):Observable<any> {

        if(gettoken!=null){
            data.gettoken=gettoken;
        }
        let json = JSON.stringify(data);
        let params = "json=" + json;
        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

        return this._http.post(this.url + "login", params, { headers });

    }
    getToken(){
        var token=localStorage.getItem("token");
        if(token && token!='undefined'){
            this.token=token;
        }else{
            this.token=null;
        }
        return token;
    }

    getIdentity(){
        let identity=JSON.parse(localStorage.getItem('identity'));
        if(identity && identity!='undefined'){
            this.identity=identity;
        }else{
            this.identity=null;
        }
        return this.identity;
    }

    update(user):Observable<any>{
        let json=JSON.stringify(user);
        let params="json="+json;
        let headers=new HttpHeaders().set('Content-Type','application/x-www-form-urlencoded').set('Authorization',this.getToken());

        return this._http.put(this.url+"user/edit",params,{headers:headers});
    }
  
}