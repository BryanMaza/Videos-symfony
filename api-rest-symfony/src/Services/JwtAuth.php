<?php

namespace App\Services;

use Firebase\JWT\JWT;
use App\Entity\User;

class JwtAuth
{

    public $manager;
    public $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = "hola_que_tal_este_es_el_master_fullstack_098809";
    }

    public function signup($email, $password, $gettoken = null)
    {

        //Comprobar si el usuario existe
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'email' => $email,
            'password' => $password
        ]);

        $signup = false;

        if (is_object($user)) {
            $signup = true;
        }
        //Generar el token de jwt
        if ($signup) {
            $token = [
                'sub' => $user->getId(),
                'name' => $user->getName(),
                'surname' => $user->getSurname(),
                'email' => $user->getEmail(),
                'iat' => time(),
                'exp' => time() * (7 * 24 * 60 * 60)
            ];
            //Codificar el token
            $jwt = JWT::encode($token, $this->key, 'HS256');
            //Comprobar el flag gettoken

            if (!empty($gettoken)) {
                $data = $jwt;
            } else {
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
                $data=$decoded;
            }
        }else{
            $data=[
                'status'=>'error',
                'code'=>200,
                'message'=>'Login incorrecto'
            ];
        }


        //Devolver los datos
        return $data;
    }

    public function checktoken($jwt, $identity=false) {
        $auth=false;

        try {
            //Por si hay problemas con el token
            //if($jwt!=null){
                $decoded=JWT::decode($jwt,$this->key,['HS256']);
            /*}else{
                $identity=false;
            }*/
           
    
        } catch (\UnexpectedValueException $e) {
            $auth=false;
        }catch(\DomainException $e){
            $auth=false;
        }
        if(isset($decoded) && !empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            $auth=true;
        }
        

        if($identity){
            return $decoded;
        }else{
            return $auth;
        }
        
        
    }
}
