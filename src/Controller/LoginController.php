<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class LoginController extends AbstractController
{
    #[Route(path: '/api/login', name: 'api_login', methods: ["POST"])]
    public function login()
    {

   $user = $this->getUser();               
     
      return $this->json([
       'username' =>$user->getUserIdentifier() ,
       'roles' =>$user->getRoles()

      ]);
    }
    #[Route(path: '/api/logout', name: 'api_logout', methods: ["POST"])]
  public function logout()
  {
  }
  

}
