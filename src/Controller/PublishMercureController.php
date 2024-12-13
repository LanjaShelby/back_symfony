<?php

namespace App\Controller;

use Exception;
use Predis\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class PublishMercureController extends AbstractController
{
   // private RedisAdapter $redis;
    private Client $Redis;

    public function __construct()
    {
        $this->Redis = new Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);
        //$redisconnnection = RedisAdapter::createConnection('redis://127.0.0.1:6379');
        //$this->redis = new RedisAdapter($redisconnnection);
    }
    #[Route('/publish/mercure', name: 'app_publish_mercure')]
    public function publish(): JsonResponse
    {

        try {
            // Publier un message dans Redis (simulé ici comme stockage d'une clé)
            $channel = 'test_channel';
            $message = [
                'user' => 'TestUser',
                'content' => 'This is a second test message',
               
            ];

            // Écriture d'une clé pour simuler une publication
            $this->Redis->publish($channel, json_encode($message));

            return new JsonResponse([
                'status' => 'Message published',
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
   

      /*  try{

            $update = new Update(
                'http://localhost/api/pizzas/{id}',
                json_encode(['status' => 'OutOfStock'])
            );
    
            $hub->publish($update);
    
            return new JsonResponse([
                'status' => 'okok'
                
            ], 400);

        }catch(Exception $e){
            return new JsonResponse([   
                'status' =>  $e
                
            ], 400);

        } */
       
    }
}
