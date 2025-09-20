<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Blameable;

/**
 * Theme entity
 *
 * Represents a training theme that groups multiple courses (cursuses).
 *
 * Uses:
 * - Timestampable: Tracks creation and update times
 * - Blameable: Tracks which user created or updated the entity
 *
 * Relationships:
 * - Cursus: One-to-many relationship
 */
#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Theme
{
    use Timestampable;
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Cursus::class, mappedBy: 'theme', orphanRemoval: true)]
    private Collection $cursuses;

    public function __construct()
    {
        $this->cursuses = new ArrayCollection();
    }

    // --- Getters and setters ---

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Cursus>
     */
    public function getCursuses(): Collection
    {
        return $this->cursuses;
    }

    public function addCursus(Cursus $cursus): static
    {
        if (!$this->cursuses->contains($cursus)) {
            $this->cursuses->add($cursus);
            $cursus->setTheme($this);
        }
        return $this;
    }

    public function removeCursus(Cursus $cursus): static
    {
        if ($this->cursuses->removeElement($cursus)) {
            if ($cursus->getTheme() === $this) {
                $cursus->setTheme(null);
            }
        }
        return $this;
    }
}
