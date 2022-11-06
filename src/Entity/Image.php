<?php

namespace App\Entity;



use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Valid()]
    #[ORM\Column(length: 255)]
    #[Assert\Valid()]
    private ?string $pathImg = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Trick $trick = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPathImg(): ?string
    {
        return $this->pathImg;
    }

    public function setPathImg(?string $pathImg): self
    {
        $this->pathImg = $pathImg;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}