<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class PasswordChangeController extends AbstractController
{
    #[Route('/api/password/change', name: 'app_password_change', methods: ['POST'])]
    public function changePassword(
        Request $request,
        Security $security,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UsersRepository $usersRepository
    ): JsonResponse {
        
        $user = $this->getUser();
$currentUser = $usersRepository->find($user);
        if (!$user) {
            return $this->json([
                'no user connected'
            ], 400);
        }

        $data = json_decode($request->getContent(), true);
        //$currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

           $currentUser->setPassword(
            $passwordHasher->hashPassword(
                $currentUser,
                $newPassword
            ));
            //$entityManager->persist($currentUser);
            $entityManager->flush(); 
        

        return $this->json([
            'success' 
        ], 200);
     

        
    }
}
