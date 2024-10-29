<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Repository\ReplyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use App\Controller\GetReplyController;
use App\Controller\SendReplyController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReplyRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    
    operations:  [
        new Get(   normalizationContext: ['groups' => ['reply:item:get']] ),
        new GetCollection(   normalizationContext: ['groups' => ['reply:collection:get']] ),
        new Post( 
            uriTemplate: '/sendreply', 
            controller: SendReplyController::class,
            deserialize: false,
            stateless: true
         ),
         new Post(
            name:'getreply',
            uriTemplate: '/getreply', 
            controller: GetReplyController::class,
            deserialize: false,
            stateless: true
         ),
        new delete(),
        new Patch(),

    ]


    
)]
class Reply
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?string $statut = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?string $message_reply = null;

    #[ORM\ManyToOne(inversedBy: 'replies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?Users $sender = null;

    #[ORM\ManyToOne(inversedBy: 'replies')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?Users $recipient = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?string $senderName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?string $recipientName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?string $senderService = null;

    #[ORM\Column(length: 255)]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?string $recipientService = null;

    #[ORM\Column]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['reply:collection:get' ,'reply:item:get'])]
    private ?bool $is_read = false;

    #[ORM\ManyToOne(inversedBy: 'replymessage')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Messages $message = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void{
 
     $this->created_at = new DateTimeImmutable();
 
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMessageReply(): ?string
    {
        return $this->message_reply;
    }

    public function setMessageReply(string $message_reply): static
    {
        $this->message_reply = $message_reply;

        return $this;
    }

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): static
    {
        $this->sender = $sender;

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

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function setSenderName(string $senderName): static
    {
        $this->senderName = $senderName;

        return $this;
    }

    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    public function setRecipientName(string $recipientName): static
    {
        $this->recipientName = $recipientName;

        return $this;
    }

    public function getSenderService(): ?string
    {
        return $this->senderService;
    }

    public function setSenderService(string $senderService): static
    {
        $this->senderService = $senderService;

        return $this;
    }

    public function getRecipientService(): ?string
    {
        return $this->recipientService;
    }

    public function setRecipientService(string $recipientService): static
    {
        $this->recipientService = $recipientService;

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

    public function isRead(): ?bool
    {
        return $this->is_read;
    }

    public function setRead(bool $is_read): static
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getMessage(): ?Messages
    {
        return $this->message;
    }

    public function setMessage(?Messages $message): static
    {
        $this->message = $message;

        return $this;
    }
}
