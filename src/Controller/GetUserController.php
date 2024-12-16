<?php

namespace App\Controller;

use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetUserController extends AbstractController
{

    public function __invoke( UsersRepository $user )
    {
        $user_service = $user->findBy(['service'=>null]);
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

        }
        return new JsonResponse($data);
    }
}
