<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ServicesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class GetAllUserController extends AbstractController
{
    public function __invoke( Request $request , UsersRepository $user , ServicesRepository $service , HubInterface $hub)
    {
        $Post = $request->request->all();
        $find_service = $service->find($Post['service']);
        $user_service = $user->findBy([
            'service'=>$find_service
            
        ]);
       
        $data=[];
        foreach ($user_service as $user_ser) {
            $data[] =[
                'id' => $user_ser->getId(),
                'email' => $user_ser->getUserIdentifier(),
                'roles' => $user_ser->getRoles(),
                'name' => $user_ser->getName(),
                'service' => $user_ser->getService()->getLibelleService(),
                'fonction' => $user_ser->getFonction(),
                'phone' => $user_ser->getPhone(),
                'image' => $user_ser->getImage()
            ];
            /*
            $update = new Update(
                'http://localhost/api/pizzas/{id}',
                json_encode(['status' => 'OutOfStock'])
            );
    
            $hub->publish($update);
    */
          

        }
        return new JsonResponse($data);
    }
}

