<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetCountController extends AbstractController
{
    public function __invoke(Request $request , EntityManagerInterface $entityManager  , ServicesRepository $service )
    {
        $MessagePost = $request->request->all();
       
        $servicePost = $MessagePost['service'];
        
    
        if(!$MessagePost || !$servicePost){
            return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
        }

       
        $recipientService = $service->find($servicePost);

       // dd($recipientService);
        $repository = $entityManager->getRepository(Messages::class);
        $messages = $repository->findBy(
            ['recipient_service' => $recipientService, 'is_read' => false],
            ['created_at' => 'DESC']
        );
        return new JsonResponse($messages);
    }
}
