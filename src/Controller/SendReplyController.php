<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\Reply;
use App\Repository\MessagesRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SendReplyController extends AbstractController
{
   public function __invoke(Request $request , EntityManagerInterface $entityManagerInterface , UsersRepository $usersRepository , MessagesRepository $messagesRepository)
   {
    $ReplyPost = $request->request->all();

    $message = $messagesRepository->find($ReplyPost['message']);
    
    $sender = $usersRepository->find($ReplyPost['sender']);
    $sender_name = $sender->getName();
    $sender_service = $sender->getService()->getLibelleService();
    $recipient = $usersRepository->find($ReplyPost['recipient']);
    $recipient_name = $recipient->getName();
    $recipient_service = $recipient->getService()->getLibelleService();

    $Reply = new Reply();
    $Reply->setStatut($ReplyPost['statut']);
    $Reply->setMessage($message);
    $Reply->setMessageReply($ReplyPost['message_reply']);
    $Reply->setSender($sender);
    $Reply->setSenderName($sender_name);
    $Reply->setSenderService($sender_service);
    $Reply ->setRecipient($recipient);
    $Reply->setRecipientName($recipient_name);
    $Reply->setRecipientService($recipient_service);

   $entityManagerInterface->persist($Reply);  
   $entityManagerInterface->flush();

   return $this->json([
      'message' => 'reply envoyer avec succès',
  ]);
   }


}

