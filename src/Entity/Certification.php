<?php

namespace App\Entity;

use App\Repository\CertificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Blameable;

/**
 * Certification entity
 *
 * Represents a certification obtained by a user for completing a specific cursus.
 *
 * Uses:
 * - Timestampable: Tracks creation and update times
 * - Blameable: Tracks which user created or updated the entity
 */
#[ORM\Entity(repositoryClass: CertificationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Certification
{
    use Timestampable;
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cursus $cursus = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $obtainedAt = null;

    /**
     * Constructor
     *
     * Initializes obtainedAt to the current datetime.
     */
    public function __construct()
    {
        $this->obtainedAt = new \DateTimeImmutable();
    }

    /**
     * Get the ID of the certification.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the user associated with this certification.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the user for this certification.
     *
     * @param User|null $user
     * @return static
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get the cursus associated with this certification.
     *
     * @return Cursus|null
     */
    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    /**
     * Set the cursus for this certification.
     *
     * @param Cursus|null $cursus
     * @return static
     */
    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;
        return $this;
    }

    /**
     * Get the date when the certification was obtained.
     *
     * @return \DateTimeImmutable|null
     */
    public function getObtainedAt(): ?\DateTimeImmutable
    {
        return $this->obtainedAt;
    }

    /**
     * Set the date when the certification was obtained.
     *
     * @param \DateTimeImmutable $obtainedAt
     * @return static
     */
    public function setObtainedAt(\DateTimeImmutable $obtainedAt): static
    {
        $this->obtainedAt = $obtainedAt;
        return $this;
    }
}
