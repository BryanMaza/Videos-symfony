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

use Knp\Component\Pager\PaginatorInterface;
use App\Services\JwtAuth;
use Firebase\JWT\JWT;
use PHPUnit\Framework\MockObject\Builder\Identity;


class VideoController extends AbstractController
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
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/VideoController.php',
        ]);
    }

    public function newVideo(Request $request, JwtAuth $jwt_auth, $id = null)
    {


        //obtener token
        $token = $request->headers->get('Authorization', null);
        $em = $this->getDoctrine()->getManager();
        //obtener el usuario
        $decoded = $jwt_auth->checktoken($token);

        //Respuesta por defecto
        $data = [
            'status' => 'error',
            'code' => 400,
            'message' => 'No se ha podido crear el video'

        ];
        //Validar los datos
        if ($decoded) {
            $identity = $jwt_auth->checktoken($token, true);
            //Recoger los datos de post
            $json = $request->get('json', null);
            $params = json_decode($json);


            if ($json != null) {

                $user_id = (($identity->sub != null)) ? $identity->sub : null;
                $title = (!empty($params->title)) ? $params->title : null;

                $description = (!empty($params->description)) ? $params->description : null;
                $url = (!empty($params->url)) ? $params->url : null;


                if (!empty($user_id) && !empty($title)) {

                    $user_repo = $this->getDoctrine()->getRepository(User::class);

                    //Realizar busqueda de user

                    $user = $user_repo->findOneBy(array(
                        'id' => $user_id
                    ));
                    if ($id == null) {
                        //Crear el objeto de video
                        $video = new Video();
                        $video->setTitle($title);
                        $video->setUser($user);
                        $video->setDescription($description);
                        $video->setUrl($url);

                        $video->setCreatedAt(new \DateTime('now'));
                        $video->setUpdatedAt(new \DateTime('now'));

                        //Guardar en la db 
                        $em->persist($video);
                        $em->flush();

                        $data = [
                            'status' => 'success',
                            'code' => 200,
                            'message' => 'Video creado correctamente',
                            'video' => $video

                        ];
                    } else {
                        //Llamar al repositorio
                        $video_rep = $this->getDoctrine()->getRepository(Video::class);
                        $em = $this->getDoctrine()->getManager();

                        $video = $video_rep->findOneBy(array(
                            'id' => $id
                        ));

                        if ($video && is_object($video) && $user_id == $video->getUser()->getId()) {

                            $video->setTitle($title);
                            $video->setDescription($description);
                            $video->setUrl($url);

                            //Guardar en la db 
                            $em->persist($video);
                            $em->flush();

                            $data = [
                                'status' => 'success',
                                'code' => 200,
                                'message' => 'Video Actualizado',
                                'video' => $video

                            ];
                        }else{
                            $data = [
                                'status' => 'error',
                                'code' => 404,
                                'message' => 'Video no encontrado'
                                

                            ];
                        }
                    }
                } else {
                    $data = [
                        'status' => 'error',
                        'code' => 400,
                        'message' => 'Datos incorrectos'

                    ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 400,
                    'message' => 'Error al enviar los datos'

                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'code' => 400,
                'message' => 'Token incorrecto'

            ];
        }

        return $this->resjson($data);
    }

    public function videos(Request $request, JwtAuth $jwt_auth, PaginatorInterface $paginator)
    {

        //REsultado por defecto
        $data = [
            'status' => 'error',
            'code' => 404,
            'message' => 'No se ha podido mostrar los videos'
        ];
        //Recoger el token
        $token = $request->headers->get('Authorization');
        //Comprobar el token
        $decoded = $jwt_auth->checktoken($token);

        if ($decoded) {
            //Conseguir la identidad del usuario
            $identity = $jwt_auth->checktoken($token, true);

            $em = $this->getDoctrine()->getManager();
            //Hacer una consulta para paginar
            $dql = "SELECT v FROM App\Entity\Video v WHERE v.user={$identity->sub} ORDER BY v.id DESC";

            $query = $em->createQuery($dql);
            //Recoger el parametro page de la url
            $page = $request->query->getInt('page', 1);
            $items_per_page = 5;

            //Invocar paginacion
            $pagination = $paginator->paginate($query, $page, $items_per_page);

            //Devulve el numero de items encontrados
            $total = $pagination->getTotalItemCount();
            //Generar respuesta

            $data = [
                'status' => 'success',
                'code' => 200,
                'total_items_count' => $total,
                'page_actual' => $page,
                'items_per_page' => $items_per_page,
                'total' => ceil($total / $items_per_page),
                'videos' => $pagination,
                'user_id' => $identity->sub
            ];
        }
        return $this->resjson($data);
    }

    public function video(Request $request, JwtAuth $jwt_auth, $id = null)
    {

        //Recibir token
        $token = $request->headers->get('Authorization');

        //Respuesta por defecto
        $data = [
            'status' => 'error',
            'code' => 404,
            'message' => 'No se puede mostrar el video'
        ];
        //Decodificar el token
        $decoded = $jwt_auth->checktoken($token);

        //Comprobar token
        if ($decoded) {

            //obtener el usuario
            $identity = $jwt_auth->checktoken($token, true);
            //obtener el repositorio
            $video_rep = $this->getDoctrine()->getRepository(Video::class);

            //Buscar el video por su id
            $video = $video_rep->findOneBy(array(
                'id' => $id
            ));


            if ($video && is_object($video) && $identity->sub == $video->getUser()->getId()) {

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'video' => $video
                ];
            } else {

                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'video' => 'No se encontro el video'
                ];
            }
        }
        //Devolver respuesta
        return $this->resjson($data);
    }

    public function remove(Request $request, JwtAuth $jwt_auth, $id = null)
    {

        //recoger el token
        $token = $request->headers->get('Authorization');
        //Decodificar el token
        $decoded = $jwt_auth->checktoken($token);

        //DEvolucion  de datos por defecto
        $data = [
            'status' => 'error',
            'code' => 404,
            'message' => 'No se ha borrado el video'
        ];
        //Validar el token
        if ($decoded) {
            //recogemos usuairo identificado
            $identity = $jwt_auth->checktoken($token, true);

            //llamar al repositorio
            $video_rep = $this->getDoctrine()->getRepository(Video::class);
            $em = $this->getDoctrine()->getManager();

            //Busqueda del video por el id
            $video = $video_rep->findOneBy(array(
                'id' => $id
            ));
            //Comprobar si existe el video y es dueño del video
            if ($video && is_object($video) && $identity->sub == $video->getUser()->getId()) {

                //Borramos el video
                $em->remove($video);
                $em->flush();

                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'video' => $video
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El video no se borró'
                ];
            }


            //Devolver respuesta
            return $this->resjson($data);
        }
    }
}
