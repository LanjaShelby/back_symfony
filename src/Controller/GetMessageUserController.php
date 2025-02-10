<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\Services;
use App\Repository\ServicesRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetMessageUserController extends AbstractController
{

    public function __invoke(Request $request , EntityManagerInterface $entityManager , UsersRepository $user , ServicesRepository $service )
    {
        $MessagePost = $request->request->all();
       
        $servicePost = $MessagePost['service'];
    
        //$userPost = $MessagePost['user'];
    
        if(!$MessagePost || !$servicePost){
            return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
        }

        
        $recipientService = $service->find($servicePost);

       // dd($recipientService);
        $repository = $entityManager->getRepository(Messages::class);
        $messages = $repository->findBy(
            ['recipient_service' => $recipientService , 'is_delete' => false ],
            ['created_at' => 'DESC'],
           
        );
        
      
        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'title' => $message->getTitle(),
                'message' => $message->getMessage(),
                'created_at' => $message->getCreatedAt()->format('c'),
                'sender' => [
                    'name' => $message->getSender()->getName(),
                    'roles' => $message->getSender()->getRoles(),
                    'image' => $message->getSender()->getImage(),
                ],
                'senderName' => $message->getSenderName(),
                'recipientName' => $message->getRecipientName(),
                'recipient_service' => [
                    'libelle_name' => $message->getRecipientService()->getLibelleService(),
                    'secteur' => $message->getRecipientService()->getSecteur(),
                ],
                'is_read' => $message->isRead(),
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
