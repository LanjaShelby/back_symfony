<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\PublisherInterface;

class RegisterController extends AbstractController
{

    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }
    public function __invoke(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager , SluggerInterface $slugger , ServicesRepository $service )
    {
        $user = new Users();
        
        $UserPost = $request->request->all();

        $ServicePost = $UserPost['service'];
       
        $new_service = $service->find($ServicePost);
       
        $FilePost = $request->files->get('image');

        if(!$UserPost){
            return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
        }
        if(!$FilePost){
            return new JsonResponse(['error' => 'Aucun fichier reçu'], 400);
        }
       // $services = $service->find($UserPost['service']);

         $user->setName($UserPost['name']);
         $user->setEmail($UserPost['email']);
         $user->setFonction($UserPost['fonction']);
         $user->setRoles($UserPost['roles']);
         $user->setPhone($UserPost['phone']);
         $user->setService($new_service);
         //$user->setService($services);
        if(!empty( $FilePost)){
            if (in_array($FilePost->guessExtension(), ["png", "jpg", "jpeg"], true)) {

                $originalName = pathinfo($FilePost->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName .'_'. uniqid() . '.' . $FilePost->guessExtension(); 
                $filePath = '/public/files/UserImage';
                try{
                    $FilePost->move(
                        $this->getParameter('kernel.project_dir') .$filePath , $newFileName
                    );

                }catch(FileException $exception){
                    return new JsonResponse($exception);
                } 
                $user->setImage($newFileName);
                 
            }else{
                return new JsonResponse(['error' => 'Type de fichier incorrect'], 400);
            }
              $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $UserPost['password']
                )
                );
        }
        $entityManager->persist($user);
        $entityManager->flush();

    //        $update = new Update(
    //     ["http://127.0.0.1:8000/api/userss/{$user->getId()}"],
    //     json_encode(['status' => 'message recu'])
    // );


    // $hub->publish($update);


    $update = new Update(
        'http://127.0.0.1:8000/api/userss/' . $user->getId(),
        json_encode(['user' => $user], JSON_THROW_ON_ERROR)
    );

    $this->publisher->__invoke($update);


        return new JsonResponse(['success' => 'Registration reusii'], 200);
    }
}
