<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * Trait Blameable
 *
 * Provides automatic tracking of which user created or last updated an entity.
 *
 * Fields:
 * - createdBy: Reference to the User who created the entity
 * - updatedBy: Reference to the User who last updated the entity
 *
 * Methods:
 * - getCreatedBy(): Returns the user who created the entity
 * - setCreatedBy(User|null $user): Sets the creator of the entity
 * - getUpdatedBy(): Returns the user who last updated the entity
 * - setUpdatedBy(User|null $user): Sets the last updater of the entity
 */
trait Blameable
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $updatedBy = null;

    /**
     * Get the user who created the entity.
     *
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * Set the user who created the entity.
     *
     * @param User|null $user
     * @return self
     */
    public function setCreatedBy(?User $user): self
    {
        $this->createdBy = $user;
        return $this;
    }

    /**
     * Get the user who last updated the entity.
     *
     * @return User|null
     */
    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * Set the user who last updated the entity.
     *
     * @param User|null $user
     * @return self
     */
    public function setUpdatedBy(?User $user): self
    {
        $this->updatedBy = $user;
        return $this;
    }
}
