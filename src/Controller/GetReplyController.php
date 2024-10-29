<?php

namespace App\Controller;

use App\Entity\Reply;
use App\Repository\MessagesRepository;
use App\Repository\ReplyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetReplyController extends AbstractController
{
   public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface , MessagesRepository $messagesRepository , ReplyRepository $replyRepository)
   {
    $MessagePost = $request->request->all();

    $message = $messagesRepository->find($MessagePost['message']);

    if(!$MessagePost ){
        return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
    }
  
    $replys = $replyRepository->findBy(
        ['message' => $message],
        ['created_at' => 'DESC']
    );
    $data = [];
    foreach($replys as $reply){
        $data[] = [
            'id' => $reply->getId(),
            'statut' => $reply->getStatut(),
            'message' => $reply->getMessageReply(),
            'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            'sender' => [
                'name' => $reply->getSender()->getName(),
                'roles' => $reply->getSender()->getRoles(),
                
            ],
            'recipient' => [
                'name' => $reply->getRecipient()->getName(),
                'roles' => $reply->getRecipient()->getRoles(),
                
            ],
            
            'senderName' => $reply->getSenderName(),
            'recipientName' => $reply->getRecipientName(),
            'recipient_service' => $reply->getRecipientService(),
            
        ];
    }
     return new JsonResponse($data);
   }
}
