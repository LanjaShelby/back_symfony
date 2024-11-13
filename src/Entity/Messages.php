<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Controller\GetMessageUserController;
use App\Controller\GetMessageSendController;
use App\Controller\GetMessageUserSendController;
use App\Controller\GetMessageController;
use App\Controller\SendMessageController;
use App\Repository\MessagesRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\New_;
use Symfony\Component\Config\Builder\Method;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Config\ApiPlatform\MercureConfig;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    
    operations:  [
        new Get(   normalizationContext: ['groups' => ['Message:item:get']] ),
        new GetCollection(   normalizationContext: ['groups' => ['Message:collection:get']] ),
        new Post( 
            uriTemplate: '/sendmessage', 
            controller: SendMessageController::class,
            deserialize: false,
            stateless: true
         ),
         new Post(
            name:'ItemGetMessage',
            uriTemplate: '/itemgetmessage', 
            controller: GetMessageController::class,
            deserialize: false,
            stateless: true
         ),
         new Post(
            name:'ItemGetMessageSend',
            uriTemplate: '/itemsendmessage', 
            controller: GetMessageSendController::class,
            deserialize: false,
            stateless: true
         ),
         new Post(
            name:'UserGetMessageSend',
            uriTemplate: '/usersendmessage', 
            controller: GetMessageUserSendController::class,
            deserialize: false,
            stateless: true
         ),
         new Post(
            name:'UserGetMessage',
            uriTemplate: '/usergetmessage', 
            controller: GetMessageUserController::class,
            deserialize: false,
            stateless: true
         ),
        new delete(),
        new Patch(),

    ]


    
)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['Message:collection:get' ,'Message:item:get','notif:collection:get' ])]
    private ?int $id = null;

    #[ORM\Column(length: 155)]
    #[Groups(['Message:collection:get' ,'Message:item:get','notif:collection:get' ])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['Message:collection:get' , 'Message:item:get'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['Message:collection:get'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['Message:collection:get' , 'Message:item:get'] )]
    private ?bool $is_read = false;

    #[ORM\ManyToOne(inversedBy: 'sent')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Message:collection:get' , 'Message:item:get'])]
    private ?Users $sender = null;



    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'message' , cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['Message:collection:get' ,'Message:item:get'])]
    private Collection $files;

    #[ORM\Column(length: 150)]
    #[Groups(['Message:collection:get' ,'Message:item:get'])]
    private ?string $senderName = null;

    #[ORM\Column(length: 150)]
    #[Groups(['Message:collection:get' ,'Message:item:get'])]
    private ?string $recipientName = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Services $recipient_service = null;

    #[ORM\Column]
    #[Groups(['Message:collection:get' ,'Message:item:get'])]
    private ?bool $is_delete = false;

    #[ORM\Column(length: 100)]
     #[Groups(['Message:collection:get' ,'Message:item:get'])]
    private ?string $SenderService = null;

    /**
     * @var Collection<int, Reply>
     */
    #[ORM\OneToMany(targetEntity: Reply::class, mappedBy: 'message',orphanRemoval: true)]
    private Collection $replymessage;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'message_id')]
    private Collection $notification;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $deletedBy = null;

    #[ORM\PrePersist]
   public function setCreatedAtValue(): void{

    $this->created_at = new DateTimeImmutable();

   }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

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

    public function getSender(): ?Users
    {
        return $this->sender;
    }

    public function setSender(?Users $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

   

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): static
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setMessage($this);
        }

        return $this;
    }

    public function removeFile(File $file): static
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getMessage() === $this) {
                $file->setMessage(null);
            }
        }

        return $this;
    }
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->replymessage = new ArrayCollection();
        $this->notification = new ArrayCollection();
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

    public function getRecipientService(): ?Services
    {
        return $this->recipient_service;
    }

    public function setRecipientService(?Services $recipient_service): static
    {
        $this->recipient_service = $recipient_service;

        return $this;
    }

    public function isDelete(): ?bool
    {
        return $this->is_delete;
    }

    public function setDelete(bool $is_delete): static
    {
        $this->is_delete = $is_delete;

        return $this;
    }

    public function getSenderService(): ?string
    {
        return $this->SenderService;
    }

    public function setSenderService(string $SenderService): static
    {
        $this->SenderService = $SenderService;

        return $this;
    }

    /**
     * @return Collection<int, Reply>
     */
    public function getReplymessage(): Collection
    {
        return $this->replymessage;
    }

    public function addReplymessage(Reply $replymessage): static
    {
        if (!$this->replymessage->contains($replymessage)) {
            $this->replymessage->add($replymessage);
            $replymessage->setMessage($this);
        }

        return $this;
    }

    public function removeReplymessage(Reply $replymessage): static
    {
        if ($this->replymessage->removeElement($replymessage)) {
            // set the owning side to null (unless already changed)
            if ($replymessage->getMessage() === $this) {
                $replymessage->setMessage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotification(): Collection
    {
        return $this->notification;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notification->contains($notification)) {
            $this->notification->add($notification);
            $notification->setMessageId($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notification->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getMessageId() === $this) {
                $notification->setMessageId(null);
            }
        }

        return $this;
    }

    public function getDeletedBy(): ?string
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?string $deletedBy): static
    {
        $this->deletedBy = $deletedBy;

        return $this;
    }
}
