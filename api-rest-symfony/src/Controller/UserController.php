<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Video;
use Dotenv\Validator;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validation;

use App\Services\JwtAuth;
use PHPUnit\Framework\MockObject\Builder\Identity;

class UserController extends AbstractController
{
    private function resjson($data)
    {
        //Serializar datos con servicios serializer
        $json = $this->get('serializer')->serialize($data, 'json');

        //Response con httpfoundation
        $response = new Response();

        //Asiganar contenido a la respuesta
        $response->setContent($json);
        //indicar formato de respuesta
        $response->headers->set('Content-Type', 'application/json');
        //Devolver la respuesta
        return $response;
    }

    public function index()
    {

        $user_repo = $this->getDoctrine()->getRepository(User::class);
        $video_repo = $this->getDoctrine()->getRepository(Video::class);

        $users = $user_repo->findAll();
        $videos = $video_repo->findAll();
        $user = $user_repo->findAll(1);

        $data = [
            'message' => 'Mi controlador',
            'path' => 'src/Controller/UserController.php',
        ];

        /*
        foreach ($users as $user) {
           echo "<h1>{$user->getName()} {$user->getSurname()}</h1>";

           foreach ($user->getVideos() as $video ) {
               echo"<p>{$video->getTitle()} -{$video->getDescription()}</p>";
           }

        }
        die();*/
        return $this->resjson($videos);
    }

    public function register(Request $request)
    {

        //Recoger los datos por post
        $json = $request->get('json', null);
        //Decodificar el json
        $params = json_decode($json);
        //Respuesta por defecto
        $data = [
            'status' => 'error',
            'code' => 200,
            'message' => 'El usuario no se ha creado',

        ];
        //Comprobar y validar datos
        if ($json != null) {
            $name = (!empty($params->name)) ? $params->name : null;
            $surname = (!empty($params->surname)) ? $params->surname : null;
            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;

            $validator = Validation::createValidator();

            //Validar email
            $validate_email = $validator->validate($email, [
                new Email()
            ]);

            if (!empty($email) && count($validate_email) == 0 && !empty($password) && !empty($name) && !empty($surname)) {

                //Crear el obejto del usuario
                $user = new User();
                $user->setName($name);
                $user->setSurname($surname);
                $user->setEmail($email);
                $user->setRole('ROLE_USER');
                $user->setCreatedAt(new \DateTime('now'));

                
                //cifrar la contraseñ
                $pwd = hash('sha256', $password);
                $user->setPassword($pwd);

                $data = $user;

                //Comprobar si el usuario existe


                $doctrine = $this->getDoctrine();
                $em = $doctrine->getManager();

                $user_repo = $doctrine->getRepository(User::class);

                //Realizara una busqueda
                $isset_user = $user_repo->findBy(array(
                    'email' => $email
                ));

                if (count($isset_user) == 0) {

                    //Guardar en la db como un objeto
                    $em->persist($user);

                    $em->flush(); //ejecuta todas las acciones y se guardaria en la db
                    $data = [
                        'status' => 'success',
                        'code' => 200,
                        'message' => 'El usuario se ha creado correctamente',
                        'user' => $user

                    ];
                } else {

                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'El usuario ya existe',

                    ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 200,
                    'message' => 'VALIDACION INCORRECTA',

                ];
            }
        }


        //Respuesta json

        return $this->resjson($data);
    }

    public function login(JwtAuth  $jwt_auth,  Request $request)
    {

        //Recibir los datos por post
        $json = $request->get('json', null);
        $params = json_decode($json);

        //Array por defecto para devolver
        $data = [
            'status' => 'error',
            'code' => 200,
            'message' => 'El usuario no ha entrado',

        ];
        //Comprobar y validar datos
        if ($json != null) {


            $email = (!empty($params->email)) ? $params->email : null;
            $password = (!empty($params->password)) ? $params->password : null;

            $gettoken = (!empty($params->gettoken)) ? $params->gettoken : null;

            $validator = Validation::createValidator();
            $validate_email = $validator->validate($email, [
                new Email()
            ]);

            if (!empty($email) && count($validate_email) == 0 && !empty($password)) {

                //Cifrar la contraseña
                $pwd = hash('sha256', $password);


                //si todo es valido, llamaremos a un servicio para identificar al usuario (jwt-token  o un objeto)

                if (!empty($gettoken)) {
                    $signup = $jwt_auth->signup($email, $pwd, $gettoken);
                } else {
                    $signup = $jwt_auth->signup($email, $pwd);
                }

                return new JsonResponse($signup);
            }
        }
        //devolver respuesta
        return $this->resjson($data);
    }

    public function edit(Request $request, JwtAuth $jwt_auth)
    {

        
        //Recoger la cabecera de atutentificacion
        $token = $request->headers->get('Authorization');
        //Comprobar si token es correct
        $auth_check = $jwt_auth->checktoken($token);

        //Respuesta por defecto

        $data = [
            'status' => 'error',
            'code' => 200,
            'msg' => 'No se puede Actualizar'
        ];

        if ($auth_check) {
           

            //Conseguir entity manager
            $em = $this->getDoctrine()->getManager();
            //Datos del usuario identificado
            $identity = $jwt_auth->checktoken($token, true);

            $user_repo = $this->getDoctrine()->getRepository(User::class);
            $user = $user_repo->findOneBy([
                'id' => $identity->sub
            ]);
            //Conseguir datos pro post

            $json = $request->get('json', null);
            $params = json_decode($json);
            //comprobar y validar los datos
           
            if ($json != null) {
                $name = (!empty($params->name)) ? $params->name : null;
                $surname = (!empty($params->surname)) ? $params->surname : null;
                $email = (!empty($params->email)) ? $params->email : null;


                $validator = Validation::createValidator();

                //Validar email
                $validate_email = $validator->validate($email, [
                    new Email()
                ]);

                if (!empty($email) && count($validate_email) == 0  && !empty($name) && !empty($surname)) {

                    //Crear el obejto del usuario
                   
                    //asignar nuevos datos
                    $user->setName($name);
                    $user->setSurname($surname);
                    $user->setEmail($email);
                    

                    //comprobar duplicados
                    $isset_user = $user_repo->findBy([
                        'email' => $email
                    ]);
                    //Guardar cambios en la base de datos
                    if (count($isset_user) == 0 || $identity->email == $email) {
                        $em->persist($user);
                      
                        $em->flush();
                        
                        $data = [
                            'status' => 'success',
                            'code' => 200,
                            'msg' => 'Usuario Actualizado',
                            'user'=>$user
                        ];
                    } else {
                        $data = [
                            'status' => 'error',
                            'code' => 400,
                            'msg' => 'No puedes usar ese email'
                        ];
                    }
                }else{
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'msg' => 'Los datos no son válidos',
                        $params
                    ];
                }
            }else{
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'msg' => 'Error al enviar los datos'
                ];
            }

        }else{
            $data = [
                'status' => 'error',
                'code' => 400,
                'msg' => 'El token es incorrecto'
            ];
        }




        return $this->resjson($data);
    }
}
