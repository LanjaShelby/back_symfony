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

class GetMessageSendController extends AbstractController
{
    public function __invoke(Request $request , EntityManagerInterface $entityManager , UsersRepository $user , ServicesRepository $service )
    {
        $MessagePost = $request->request->all();
       
        $servicePost = $MessagePost['service'];
        //$userPost = $MessagePost['user'];
    
        if(!$MessagePost || !$servicePost){
            return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
        }

        //if(!$MessagePost || !$userPost){
          //  return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
       // }
       // $recipient = $user->findOneBy(['name' =>$userPost]);
        $recipientService = $service->find($servicePost);

       // dd($recipientService);
        $repository = $entityManager->getRepository(Messages::class);
        $messages = $repository->createQueryBuilder('m')
        ->join('m.sender', 's')
        ->where('s.service = :service')
        ->setParameter('service', $recipientService)
        ->orderBy('m.created_at', 'DESC')
        ->getQuery()
        ->getResult();
        
      
        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'title' => $message->getTitle(),
                'message' => $message->getMessage(),
                'created_at' => $message->getCreatedAt()->format('c'),
                'sender' => [
                    'id' => $message ->getSender()->getId(),
                    'name' => $message->getSender()->getName(),
                    'roles' => $message->getSender()->getRoles(),
                    
                ],
                'senderName' => $message->getSenderName(),
                'recipientName' => $message->getRecipientName(),
                'recipient_service' => [
                    'libelle_name' => $message->getRecipientService()->getLibelleService(),
                    'secteur' => $message->getRecipientService()->getSecteur(),
                ],
                'senderService' => $message->getSenderService(),
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
