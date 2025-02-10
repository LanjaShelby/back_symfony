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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mime\Email;

class RegisterController extends AbstractController
{

    /*public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }*/
    public function __invoke(Request $request, MailerInterface $mailer ,UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager , SluggerInterface $slugger , ServicesRepository $service )
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

       $username = $UserPost['name'];
       $email = $UserPost['email'];
       $password = $UserPost['password'];
      $emailAddress = $UserPost['email'];
      
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


        $email = (new Email())
        ->from('bonasenesa@gmail.com')
        ->to($emailAddress)
        ->subject('Inscriptions Share')
        ->html("
            <p >Bonjour <strong>$username</strong>,</p>
            <p> Votre à bien été créer avec succès par l'administrateur de votre service</p>
            <p>Voici vos identifiants :</p>
            <ul>
                <li><strong>Nom d'utilisateur :</strong> $username</li>
                <li><strong>Adresse E-mail :</strong> $email</li>
                <li><strong>Mot de passe :</strong> $password</li>
            </ul>
            <p>Merci de vous connecter sur notre <a href='http://127.0.0.1:3000/login'>application</a>.</p>
        ");


    $mailer->send($email);
      






        return new JsonResponse(['success' => 'Registration reusii'], 200);
    }
}
