<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetNotificationController extends AbstractController
{
    public function __invoke(Request $request , EntityManagerInterface $entityManager , NotificationRepository $notification , ServicesRepository $service )
    {
        $NotificationPost = $request->request->all();
       
        $servicePost = $NotificationPost['service'];
    
        //$userPost = $MessagePost['user'];
    
        if(!$NotificationPost || !$servicePost){
            return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
        }


        $SenderService = $service->find($servicePost);
       

        // dd($SenderService->getLibelleService());
        $repository = $entityManager->getRepository(Notification::class);
        $notifications = $repository->findBy(
            ['service_sender' => $SenderService->getLibelleService()],
            ['created_at' => 'DESC'],
           
        );
       // dd($notifications);
        
      
        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'action_type' => $notification->getActionType(),
                'created_at' => $notification->getCreatedAt()->format('c'),
                'requester' => [
                    'name' => $notification->getRequesterId() ? $notification->getRequesterId()->getName() : null,
                    'roles' => $notification->getRequesterId() ? $notification->getRequesterId()->getRoles() : null,
                ],
                'sender_service' =>$notification->getServiceSender(),
                'message_id' => $notification->getMessageId()->getId(),
                'service_id_recipient'=>$notification->getService() ? $notification->getService()->getLibelleService() : null,

       
            ];
        }

        // Retourner les messages sous forme de JSON
        return new JsonResponse($data);
    }
}
