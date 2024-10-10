<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetMessageController extends AbstractController
{

    public function __invoke(Request $request , EntityManagerInterface $entityManager , UsersRepository $user )
    {
        $MessagePost = $request->request->all();
       
        $userPost = $MessagePost['user'];
    
        if(!$MessagePost || !$userPost){
            return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
        }
        $recipient = $user->findOneBy(['name' =>$userPost]);
      
        
        $repository = $entityManager->getRepository(Messages::class);
        $messages = $repository->findBy(
            ['recipient' => $recipient],
            ['created_at' => 'DESC']
        );
        
      
        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                //'id' => $message->getId(),
                'title' => $message->getTitle(),
                'message' => $message->getMessage(),
                'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'sender' => [
                    'name' => $message->getSender()->getName(),
                    'roles' => $message->getSender()->getRoles(),
                ],
                'recipient' => [
                    'name' => $message->getRecipient()->getName(),
                    'roles' => $message->getRecipient()->getRoles(),
                ],
                'files' => array_map(function ($file) {
                    return [
                        'path' => $file->getPath(),
                        'type' => $file->getTypeFile(),
                    ];
                }, $message->getFiles()->toArray()), // Convertir la collection de fichiers
            ];
        }

        // Retourner les messages sous forme de JSON
        return new JsonResponse($data);
    }
}
