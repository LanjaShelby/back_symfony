<?php

namespace App\Entity;




use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Operations;
use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\GetAllUserController;
use App\Controller\GetUserController;
use App\Controller\PatchRoleUserController;
use App\Controller\PatchUserController;
use App\Controller\RegisterController;
use App\Controller\UserInfoController;

#[ApiResource(
    
    mercure:true,
    operations:[
        new Get( normalizationContext: ['groups' => ['User:item:read']]),
        new GetCollection(
            name: 'UserMe',
            uriTemplate: '/userme', 
            controller: UserInfoController::class,
            stateless:true ),
        new GetCollection(
                name: 'UserAll',
                uriTemplate: '/userall', 
                controller: GetUserController::class,
                stateless:true ),
       new Post(
                    name: 'Userr',
                    uriTemplate: '/userr', 
                    controller: GetAllUserController::class,
                    deserialize: false,
                    stateless:true ),
        new GetCollection( normalizationContext: ['groups' => ['User:collection:read']] ),
        new Post(   
        uriTemplate: '/register', 
        controller: RegisterController::class,
        deserialize: false,
        stateless: false),
        new Delete(),
        new Patch()
       
    ]
    
)]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['User:collection:read', 'User:item:read' ,'User:item:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['User:collection:read', 'User:item:read' ,'User:item:write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['User:collection:read', 'User:item:read' , 'User:item:write' ,'Message:collection:get'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['User:item:write'])]
    private ?string $password = null;



    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'sender')]
    #[Groups(['User:collection:read', 'User:item:read'])]
    private Collection $sent;

  

    #[ORM\Column(length: 255)]
    #[Groups(['User:collection:read', 'User:item:read' ,'User:item:write','Message:collection:get','reply:collection:get','notif:collection:get'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['User:collection:read', 'User:item:read' ,'User:item:write','Message:collection:get','reply:collection:get'])]
    private ?Services $service = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['User:collection:read', 'User:item:read' ,'User:item:write'])]
    private ?string $fonction = null;

    #[ORM\Column(length: 255)]
    #[Groups(['User:collection:read', 'User:item:read' ,'User:item:write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['User:collection:read', 'User:item:read' ,'User:item:write'])]
    private ?string $image = null;

    /**
     * @var Collection<int, Reply>
     */
    #[ORM\OneToMany(targetEntity: Reply::class, mappedBy: 'sender')]
    private Collection $replies;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'requester_id')]
    private Collection $user_notif;

  
    public function __construct()
    {
        $this->sent = new ArrayCollection();
        $this->replies = new ArrayCollection();
        $this->user_notif = new ArrayCollection();
      
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    

    /**
     * @return Collection<int, Messages>
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Messages $sent): static
    {
        if (!$this->sent->contains($sent)) {
            $this->sent->add($sent);
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Messages $sent): static
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

  
  

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Reply>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(Reply $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setSender($this);
        }

        return $this;
    }

    public function removeReply(Reply $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getSender() === $this) {
                $reply->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getUserNotif(): Collection
    {
        return $this->user_notif;
    }

    public function addUserNotif(Notification $userNotif): static
    {
        if (!$this->user_notif->contains($userNotif)) {
            $this->user_notif->add($userNotif);
            $userNotif->setRequesterId($this);
        }

        return $this;
    }

    public function removeUserNotif(Notification $userNotif): static
    {
        if ($this->user_notif->removeElement($userNotif)) {
            // set the owning side to null (unless already changed)
            if ($userNotif->getRequesterId() === $this) {
                $userNotif->setRequesterId(null);
            }
        }

        return $this;
    }

   
}
