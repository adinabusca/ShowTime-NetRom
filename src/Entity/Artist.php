<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $music_genre = null;

    /**
     * @var Collection<int, FestivalArtist>
     */
    #[ORM\OneToMany(targetEntity: FestivalArtist::class, mappedBy: 'artist')]
    private Collection $performances;

    public function __construct()
    {
        $this->performances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMusicGenre(): ?string
    {
        return $this->music_genre;
    }

    public function setMusicGenre(string $music_genre): static
    {
        $this->music_genre = $music_genre;

        return $this;
    }

    /**
     * @return Collection<int, FestivalArtist>
     */
    public function getPerformances(): Collection
    {
        return $this->performances;
    }

    public function addPerformance(FestivalArtist $performance): static
    {
        if (!$this->performances->contains($performance)) {
            $this->performances->add($performance);
            $performance->setArtist($this);
        }

        return $this;
    }

    public function removePerformance(FestivalArtist $performance): static
    {
        if ($this->performances->removeElement($performance)) {
            // set the owning side to null (unless already changed)
            if ($performance->getArtist() === $this) {
                $performance->setArtist(null);
            }
        }

        return $this;
    }
}
