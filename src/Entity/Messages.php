<?php

namespace App\Entity;


use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
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
        new delete()
    ]


    
)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
   
    private ?int $id = null;

    #[ORM\Column(length: 155)]
    #[Groups(['Message:collection:get' ,'Message:item:get'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['Message:collection:get' , 'Message:item:get'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['Message:collection:get'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['Message:collection:get'] )]
    private ?bool $is_read = true;

    #[ORM\ManyToOne(inversedBy: 'sent')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Message:collection:get' , 'Message:item:get'])]
    private ?Users $sender = null;

    #[ORM\ManyToOne(inversedBy: 'received')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Message:collection:get','Message:item:get'])]
    private ?Users $recipient = null;

    /**
     * @var Collection<int, File>
     */
    #[ORM\OneToMany(targetEntity: File::class, mappedBy: 'message' , orphanRemoval: true)]
    #[Groups(['Message:collection:get'] ,['Message:item:get'])]
    private Collection $files;

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

    public function getRecipient(): ?Users
    {
        return $this->recipient;
    }

    public function setRecipient(?Users $recipient): static
    {
        $this->recipient = $recipient;

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
    }
}
