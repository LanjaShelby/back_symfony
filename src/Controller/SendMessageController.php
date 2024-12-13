<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Messages;
use App\Entity\Users;
use App\Repository\ServicesRepository;
use App\Repository\UsersRepository;
use App\Repository\FileRepository;
use Doctrine\Migrations\Tools\Console\ConsoleLogger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Predis\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


   /*
       #[ORM\ManyToOne(inversedBy: 'received')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Message:collection:get','Message:item:get'])]
    private ?Services $recipient = null;
      public function addReceived(Messages $received): static
    {
        if (!$this->received->contains($received)) {
            $this->received->add($received);
            $received->setRecipient($this);
        }

        return $this;
    }

    public function removeReceived(Messages $received): static
    {
        if ($this->received->removeElement($received)) {
            // set the owning side to null (unless already changed)
            if ($received->getRecipient() === $this) {
                $received->setRecipient(null);
            }
        }

        return $this;
    }
     public function getRecipient(): ?Users
    {
        return $this->recipient;
    }

    public function setRecipient(?Users $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }
     public function __construct(private $userSender)
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
class SendMessageController  extends AbstractController
{
    private Client $Redis;

    public function __construct()
    {
        $this->Redis = new Client([
            'scheme' => 'tcp',
            'host'   => '127.0.0.1',
            'port'   => 6379,
        ]);}

    public function __invoke(Request $request , HubInterface $hub, EntityManagerInterface $entityManager , SluggerInterface $slugger , UsersRepository $user , ServicesRepository $service , FileRepository $fileRepository, PublisherInterface $publisher)
    {
$MessagePost = $request->request->all();
/**@var  UploadedFile $file */
$FilesPost = $request->files->get('files');
 


if(!$MessagePost){
    return new JsonResponse(['error' => 'Aucun donnée reçu'], 400);
}

$sender = $user->find($MessagePost['sender']);
$senderName = $sender->getName();
$senderService = $sender->getService()->getLibelleService();


$service_recipient_name = $service->find($MessagePost['recipient']); 
// the service recipient
$serviceName = $service_recipient_name->getLibelleService();


$Message = new Messages();
$Message->setMessage($MessagePost['message']);
$Message->setTitle($MessagePost['title']);
$Message->setSender($sender);
$Message->setRecipientService($service_recipient_name);
$Message->setSenderName($senderName);
$Message->setRecipientName($serviceName);
$Message->setSenderService($senderService);
if(!empty($FilesPost)){
    foreach($FilesPost as $file){
       if($file instanceof UploadedFile){
           $originalName = pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
           $safeFileName = $slugger->slug($originalName);
             $fileName = $safeFileName . '_' . uniqid("", true) . '.' . $file->guessExtension();
                $filePath = '';
            if (in_array($file->guessExtension(), ["png", "jpg", "jpeg"], true)) {
                $filePath = '/public/files/Images';
                $typeFile = "Images";
            } elseif (in_array($file->guessExtension(), ["pdf"], true)) {
                $filePath = '/public/files/PDF';
                $typeFile = "PDF";
            } elseif (in_array($file->guessExtension(), ["doc", "docx"], true)) {
                $filePath = '/public/files/Doc';
                $typeFile = "Doc";
            } else {
                $filePath = '/public/files/Another';
                $typeFile = "Another";
            }

            // Déplacement et création du fichier
            $file->move($this->getParameter('kernel.project_dir') . $filePath, $fileName);
            $Files = new File();
            $Files->setPath($fileName);
            $Files->setSize(2);  // Définir la taille réelle si nécessaire
            $Files->setTypeFile($typeFile);
           
            $entityManager->persist($Files);

            // Associer le fichier au message
            $Message->addFile($Files); 
       }else{
        return $this->json([
            'error' => 'Fichier non valide ou introuvable.'
        ], 400);
       }
       
    }


}else{
   $fileshares = $MessagePost['fileshare'];
   if($fileshares){
        foreach($fileshares as $file) {
        $filefile = $fileRepository->findOneBy(['path' => $file ]);
        $Message->addFile($filefile);
    }
 
   }else{
    return $this->json([
        'error' => 'Fichier non valide ou introuvable.'
    ], 400);
   }
        
    
}

$entityManager->persist($Message);  
$entityManager->flush(); 

$data[] = [
    'id' => $Message->getId(),
    'title' => $Message->getTitle(),
    'message' => $Message->getMessage(),
    'created_at' => $Message->getCreatedAt()->format('c'),
    'sender' => [
        'id' => $Message ->getSender()->getId(),
        'name' => $Message->getSender()->getName(),
        'roles' => $Message->getSender()->getRoles(),
        
    ],
    'senderName' => $Message->getSenderName(),
    'recipientName' => $Message->getRecipientName(),
    'recipient_service' => [
        'libelle_name' => $Message->getRecipientService()->getLibelleService(),
        'secteur' => $Message->getRecipientService()->getSecteur(),
    ],
    'senderService' => $Message->getSenderService(),
    'is_read' => $Message->isRead(),
    'files' => array_map(function ($file) {
        return [
            'path' => $file->getPath(),
            'type' => $file->getTypeFile(),
        ];
    }, $Message->getFiles()->toArray()), // Convertir la collection de fichiers
];

try {
    // Publier un message dans Redis (simulé ici comme stockage d'une clé)
    $channel = 'test_channel';
    $message = [
        'user' => 'TestUser',
        'content' => 'This is a second test message',
       
    ];

    // Écriture d'une clé pour simuler une publication
    $this->Redis->publish($channel, json_encode($data));

    return new JsonResponse([
        'status' => 'Message published',
        'message' => $data,
    ]);
} catch (\Exception $e) {
    return new JsonResponse([
        'status' => 'error',
        'message' => $e->getMessage(),
    ], 500);
}
 
   
    return $this->json([
        'message' => 'Message envoyer avec succès',
    ]);

    }

}

/* $update = new Update(
        ["https://localhost:8000/api/messages/{$Message->getId()}"],
        json_encode(['status' => 'message recu'])
    );

    $hub->publish($update);

    $MessageMessage = $MessagePost['message'];
$title = $MessagePost['title'];
$senderredis=$sender;
$service_recipient_nameredis = $service_recipient_name;
$senderredisname = $senderName;
$setRecipientNameredis = $serviceName;
$SenderServiceredis =$senderService;

   */
    
    /*private $entityManager;
      #[Route(path: '/api/publish', name: 'publish')]
  public function publish(HubInterface $hub): Response
  {
    try {
      $update = new Update(
        'https://example.com/books/1',
        json_encode(['status' => 'message recu'])
    );

    $hub->publish($update);

    return new Response('published!');
    } catch (\Throwable $th) {
        throw $th;
    }
      
  } $update = new Update(
        "'https://example.com/books/1' ",  // Topic spécifique à l'utilisateur
        json_encode([
            'id' => $Message->getId(),
            'title' => $Message->getTitle(),
            'message' => $Message->getMessage(),
            'created_at' => $Message->getCreatedAt()->format('Y-m-d H:i:s'),
            'sender' => $Message->getSenderName(),
            'recipient' => $Message->getRecipientName(),
        ])
    );
    $hub->publish($update); 
   
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

         'data' => [
        'message_text' => $MessagePost['message'],
       
        'sender' => $MessagePost['sender'],
        'recipient' => $MessagePost['recipient'],
        'title' => $MessagePost['title']
        
    ]
        */


 
        /*if(in_array($file->guessExtension(),$fileImag,true)){
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
                    
//$content = $request->getContent();

 //$data = json_decode($content);

  //      return $this->json([ 
    //    'message' => 'Message envoyé avec succès',
      //  'data' => $data ]);
            }*/
