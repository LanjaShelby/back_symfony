<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Controller\GetNotificationController;
use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
    
    operations:  [
        new Get(   normalizationContext: ['groups' => ['notif:item:get']] ),
        new GetCollection(   normalizationContext: ['groups' => ['notif:collection:get']] ),
        new Post(
            name:'GetNotification',
            uriTemplate: '/getnotification', 
            controller: GetNotificationController::class,
            deserialize: false,
            stateless: true
         ),
        new delete(),
        new Patch(),

    ]


    
)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notif:collection:get' ,'notif:item:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notif:collection:get' ,'notif:item:get'])]
    private ?string $action_type = null;

    #[ORM\ManyToOne(inversedBy: 'notification')]
    #[ORM\JoinColumn(nullable: true,onDelete: "SET NULL")]
    #[Groups(['notif:collection:get' ,'notif:item:get'])]
    private ?Messages $message_id = null;

    #[ORM\ManyToOne(inversedBy: 'user_notif')]
    #[Groups(['notif:collection:get' ,'notif:item:get'])]
    private ?Users $requester_id = null;

    #[ORM\ManyToOne(inversedBy: 'service_notif')]
    #[Groups(['notif:collection:get' ,'notif:item:get'])]
    private ?Services $service = null;

    #[ORM\Column]
    #[Groups(['notif:collection:get' ,'notif:item:get'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $service_sender = null;

  
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActionType(): ?string
    {
        return $this->action_type;
    }

    public function setActionType(string $action_type): static
    {
        $this->action_type = $action_type;

        return $this;
    }

    public function getMessageId(): ?Messages
    {
        return $this->message_id;
    }

    public function setMessageId(?Messages $message_id): static
    {
        $this->message_id = $message_id;

        return $this;
    }

    public function getRequesterId(): ?Users
    {
        return $this->requester_id;
    }

    public function setRequesterId(?Users $requester_id): static
    {
        $this->requester_id = $requester_id;

        return $this;
    }

    public function getService(): ?Services
    {
        return $this->service;
    }

    public function setService(?Services $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getServiceSender(): ?string
    {
        return $this->service_sender;
    }

    public function setServiceSender(?string $service_sender): static
    {
        $this->service_sender = $service_sender;

        return $this;
    }

  

 

   
}
