<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FileRepository::class)]
#[ApiResource]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read'] ,['write'])]
    private ?string $path = null;

    #[ORM\Column]
    #[Groups(['read'] ,['write'])]
    private ?float $size = null;

    #[ORM\ManyToOne(inversedBy: 'files')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Messages $message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $upload_at = null;

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
}
