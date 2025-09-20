<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Blameable;

/**
 * Purchase entity
 *
 * Represents a purchase made by a user, either for a lesson or an entire course (cursus).
 *
 * Uses:
 * - Timestampable: Tracks creation and update times
 * - Blameable: Tracks which user created or updated the entity
 *
 * Relationships:
 * - User: Many-to-one relationship
 * - Lesson: Many-to-one relationship (optional if purchase is for a Cursus)
 * - Cursus: Many-to-one relationship (optional if purchase is for a Lesson)
 */
#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Purchase
{
    use Timestampable;
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null; // "lesson" or "cursus"

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    private ?Lesson $lesson = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    private ?Cursus $cursus = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $purchasedAt = null;

    /**
     * Constructor
     *
     * Initializes purchasedAt to the current datetime.
     */
    public function __construct()
    {
        $this->purchasedAt = new \DateTimeImmutable();
    }

    // --- Getters and setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the type of purchase.
     *
     * @return string|null "lesson" or "cursus"
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): static
    {
        $this->lesson = $lesson;
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

    public function getPurchasedAt(): ?\DateTimeImmutable
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(\DateTimeImmutable $purchasedAt): static
    {
        $this->purchasedAt = $purchasedAt;
        return $this;
    }
}
