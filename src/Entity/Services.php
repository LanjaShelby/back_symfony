<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ServicesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServicesRepository::class)]
#[ApiResource(
    operations:[
        new Get( normalizationContext: ['groups' => ['service:item:read']]),
        new GetCollection( normalizationContext: ['groups' => ['service:collection:read']] ),
        new Post( denormalizationContext: ['groups' => ['service:item:write']]),
        new Delete(),
        new Patch(),
    
       
    ]



)]
class Services
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service:collection:read','service:item:read','service:item:write','User:collection:read','Message:collection:get'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['service:collection:read','service:item:read','service:item:write','User:collection:read','User:item:read','Message:collection:get'])]
    private ?string $libelle_service = null;

    #[ORM\Column(length: 255)]
    #[Groups(['service:collection:read','service:item:read','service:item:write','User:collection:read','Message:collection:get'])]
    private ?string $secteur = null;

    /**
     * @var Collection<int, Users>
     */
    #[ORM\OneToMany(targetEntity: Users::class, mappedBy: 'service')]
    private Collection $users;

    /**
     * @var Collection<int, Messages>
     */
    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'recipient_service')]
    private Collection $messages;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleService(): ?string
    {
        return $this->libelle_service;
    }

    public function setLibelleService(string $libelle_service): static
    {
        $this->libelle_service = $libelle_service;

        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->secteur;
    }

    public function setSecteur(string $secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * @return Collection<int, Users>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Users $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setService($this);
        }

        return $this;
    }

    public function removeUser(Users $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getService() === $this) {
                $user->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Messages $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setRecipientService($this);
        }

        return $this;
    }

    public function removeMessage(Messages $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getRecipientService() === $this) {
                $message->setRecipientService(null);
            }
        }

        return $this;
    }
}
