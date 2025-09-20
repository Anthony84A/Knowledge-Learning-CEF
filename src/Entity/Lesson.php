<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Blameable;

/**
 * Lesson entity
 *
 * Represents a single lesson within a course (Cursus).
 *
 * Uses:
 * - Timestampable: Tracks creation and update times
 * - Blameable: Tracks which user created or updated the entity
 *
 * Relationships:
 * - Cursus: Many-to-one relationship
 * - Purchase: One-to-many relationship
 * - LessonValidation: One-to-many relationship
 */
#[ORM\Entity(repositoryClass: LessonRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Lesson
{
    use Timestampable;
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursus $cursus = null;

    #[ORM\OneToMany(targetEntity: Purchase::class, mappedBy: 'lesson')]
    private Collection $purchases;

    #[ORM\OneToMany(targetEntity: LessonValidation::class, mappedBy: 'lesson')]
    private Collection $lessonValidations;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
        $this->lessonValidations = new ArrayCollection();
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;
        return $this;
    }

    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): static
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setLesson($this);
        }
        return $this;
    }

    public function removePurchase(Purchase $purchase): static
    {
        if ($this->purchases->removeElement($purchase)) {
            if ($purchase->getLesson() === $this) {
                $purchase->setLesson(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, LessonValidation>
     */
    public function getLessonValidations(): Collection
    {
        return $this->lessonValidations;
    }

    public function addLessonValidation(LessonValidation $lessonValidation): static
    {
        if (!$this->lessonValidations->contains($lessonValidation)) {
            $this->lessonValidations->add($lessonValidation);
            $lessonValidation->setLesson($this);
        }

        return $this;
    }

    public function removeLessonValidation(LessonValidation $lessonValidation): static
    {
        if ($this->lessonValidations->removeElement($lessonValidation)) {
            if ($lessonValidation->getLesson() === $this) {
                $lessonValidation->setLesson(null);
            }
        }

        return $this;
    }
}
