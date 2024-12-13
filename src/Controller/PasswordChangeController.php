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
       /* if ($request->isMethod('POST')) {
            $currentPassword = $request->request->get('current_password');
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            // Vérifier le mot de passe actuel
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('app_change_password');
            }

            // Vérifier que les nouveaux mots de passe correspondent
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_change_password');
            }

            // Mettre à jour le mot de passe
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');

            return $this->redirectToRoute('app_home'); // Redirigez vers une page appropriée
        }*/

        
    }
}
