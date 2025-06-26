<?php

namespace App\Entity;

use App\Repository\FestivalArtistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FestivalArtistRepository::class)]
class FestivalArtist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'performances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?festival $festival = null;

    #[ORM\ManyToOne(inversedBy: 'performances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?artist $artist = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFestival(): ?festival
    {
        return $this->festival;
    }

    public function setFestival(?festival $festival): static
    {
        $this->festival = $festival;

        return $this;
    }

    public function getArtist(): ?artist
    {
        return $this->artist;
    }

    public function setArtist(?artist $artist): static
    {
        $this->artist = $artist;

        return $this;
    }
}
