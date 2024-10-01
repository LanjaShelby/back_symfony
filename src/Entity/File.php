<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operations;
use ApiPlatform\Metadata\Post;
use App\Controller\SendMessageController;
use App\Repository\FileRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\File as ConstraintsFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;



#[ORM\Entity(repositoryClass: FileRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
 operations: [
             new Get( normalizationContext: ['groups'=> ['file:item:get']]),              
             new GetCollection( normalizationContext: ['groups'=> ['file:collection:get']]),
             new Post(
               denormalizationContext: ['groups'=> ['file:item:get']],
             ) 
          ]

)]

class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 
 
    #[ORM\Column(length: 255)]
    #[Groups(['file:item:get','file:collection:get','Message:collection:get'] )]
    private ?string $path = null;

    //#[Vich\UploadableField(mapping: 'pieces', fileNameProperty: 'path', size: 'size')]
    //#[ConstraintsFile()]
    //private ?File $File = null;

    #[ORM\Column]
    #[Groups(['file:item:get' ,'file:collection:get'] )]
    private ?float $size = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['file:item:get' ,'file:collection:get'])]
    private ?Messages $message = null;

    #[ORM\Column]
    #[Groups(['file:item:get' ,'file:collection:get'])]
    private ?\DateTimeImmutable $upload_at = null;

    #[ORM\Column(length: 50)]
    #[Groups(['file:item:get' ,'file:collection:get','Message:collection:get'])]
    private ?string $TypeFile = null;

    #[ORM\PrePersist]
   public function setCreatedAtValue(): void{

    $this->upload_at = new DateTimeImmutable();

   }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(float $size): static
    {
        $this->size = $size;

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

    public function getUploadAt(): ?\DateTimeImmutable
    {
        return $this->upload_at;
    }

    public function setUploadAt(\DateTimeImmutable $upload_at): static
    {
        $this->upload_at = $upload_at;

        return $this;
    }

    public function getTypeFile(): ?string
    {
        return $this->TypeFile;
    }

    public function setTypeFile(string $TypeFile): static
    {
        $this->TypeFile = $TypeFile;

        return $this;
    }
}
