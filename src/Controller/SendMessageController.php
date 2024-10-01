<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Messages;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class SendMessageController  extends AbstractController
{
   
   
   /*  public function __construct(private $userSender)
    {
       $user = $this->getUser();
       $this->userSender = $user->getUserIdentifier();
       dd($this->userSender);
    }
$message_recipient = $request->get('recipient');
$message_sender = $request->get('sender');
$message_title = $request->get('title');
$message_content = $request->get('message');
dd($file->getClientOriginalName() , $file->getClientOriginalExtension() , $file->getClientMimeType());
$data = base64_decode($file);
 orphanRemoval: false
 public function __construct(private Security $security)
{
 $userSender = $this->security->getUser();
}
*/

    public function __invoke(Request $request , EntityManagerInterface $entityManager , UsersRepository $user)
    {
$MessagePost = $request->request->all();

/**@var  UploadedFile $file */
$FilesPost = $request->files->get('files');

if(!$MessagePost){
    return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
}
if(!$FilesPost){
    return new JsonResponse(['error' => 'Aucun fichier reçu'], 400);
}

$sender = $user->find($MessagePost['sender']);
$recipient = $user->find($MessagePost['recipient']);


$Message = new Messages();
$Message->setMessage($MessagePost['message']);
$Message->setTitle($MessagePost['title']);
$Message->setSender($sender);
$Message->setRecipient($recipient);

if(!empty($FilesPost)){
    foreach($FilesPost as $file){
       if($file instanceof UploadedFile){
        $fileImag= ["PNG","png","jpg","JPG","JPEG","jpeg"];
        $filePDF = ["pdf","PDF"];
        $fileDOC = ["docx","doc"];
            if(in_array($file->guessExtension(),$fileImag,true)){
                    $fileName =  $file->getClientOriginalName(). '-' .uniqid("",true). '.'. $file->guessExtension() ;
                    $file->move($this->getParameter('kernel.project_dir') .'/public/files/Images' , $fileName);
                    $Files = new File();
                    $Files->setPath($fileName);
                    $Files->setSize(2);
                    $Files->setTypeFile("Images");
                    $entityManager->persist($Files);
                    $Message->addFile($Files); 
            
            }elseif(in_array($file->guessExtension(),$filePDF,true)){
                    $fileName =  $file->getClientOriginalName(). '-' .uniqid("",true). '.'. $file->guessExtension() ;
                    $file->move($this->getParameter('kernel.project_dir') .'/public/files/PDF' , $fileName);
                    $Files = new File();
                    $Files->setPath($fileName);
                    $Files->setSize(2);
                    $Files->setTypeFile("PDF");
                    $entityManager->persist($Files);
                    $Message->addFile($Files);
                
            }elseif(in_array($file->guessExtension(),$fileDOC,true)){
                    $fileName =  $file->getClientOriginalName(). '-' .uniqid("",true). '.'. $file->guessExtension() ;
                    $file->move($this->getParameter('kernel.project_dir') .'/public/files/Doc' , $fileName);
                    $Files = new File();
                    $Files->setPath($fileName);
                    $Files->setSize(2);
                    $Files->setTypeFile("Doc");
                    $entityManager->persist($Files);
                    $Message->addFile($Files);
            }else{
                    $fileName =  $file->getClientOriginalName(). '-' .uniqid("",true). '.'. $file->guessExtension() ;
                    $file->move($this->getParameter('kernel.project_dir') .'/public/files/Another' , $fileName);
                    $Files = new File();
                    $Files->setPath($fileName);
                    $Files->setSize(2);
                    $Files->setTypeFile("Another");
                    $entityManager->persist($Files);
                    $Message->addFile($Files);
            }
       }

    }


}else{
    return $this->json([
        'error' => 'Probleme parcours des fichiers'
    ], 400);
}


$entityManager->persist($Message);
$entityManager->flush();

if (!$FilesPost || !$MessagePost) {
    return $this->json([
        'error' => 'Les fichiers ou le message sont manquants'
    ], 400);
}



return $this->json([
    'message' => 'Message reçu avec succès',
    'data' => [
        'message_text' => $MessagePost['message'],
       
        'sender' => $MessagePost['sender'],
        'recipient' => $MessagePost['recipient'],
        'title' => $MessagePost['title']
        
    ]
]);
//$content = $request->getContent();

 //$data = json_decode($content);

  //      return $this->json([ 
    //    'message' => 'Message envoyé avec succès',
      //  'data' => $data ]);

    }

}
    
    /*private $entityManager;
    
    public function __construct( EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function __invoke(Request $request): Response
    {
        $uploadedfile = $request->file->get('file');
        if (!$uploadedfile) {
            throw new BadRequestHttpException('File is required');
        }

        $piece = new File();
        $piece->setPath($uploadedfile);
        */


 

