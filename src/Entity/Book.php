<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\Column(type: 'boolean')]
    private $available;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'books')]
    private $client;

    #[ORM\Column(type: 'date')]
    private $disponibility;

    #[ORM\Column(type: 'dateinterval')]
    private $maxDisponibility;

    #[ORM\Column(type: 'string', length: 255)]
    private $cover;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDisponibility(): ?\DateTimeInterface
    {
        return $this->disponibility;
    }

    public function setDisponibility(\DateTimeInterface $disponibility): self
    {
        $this->disponibility = $disponibility;

        return $this;
    }

    public function getMaxDisponibility(): ?\DateInterval
    {
        return $this->maxDisponibility;
    }

    public function setMaxDisponibility(\DateInterval $maxDisponibility): self
    {
        $this->maxDisponibility = $maxDisponibility;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }
}
