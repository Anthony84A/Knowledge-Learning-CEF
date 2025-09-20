<?php
namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait Timestampable
 *
 * Provides automatic tracking of creation and update timestamps for an entity.
 *
 * Fields:
 * - createdAt: Stores the datetime when the entity was created
 * - updatedAt: Stores the datetime when the entity was last updated
 *
 * Methods:
 * - getCreatedAt(): Returns the creation datetime
 * - setCreatedAt(DateTimeImmutable $dt): Sets the creation datetime
 * - getUpdatedAt(): Returns the last update datetime
 * - setUpdatedAt(?DateTimeImmutable $dt): Sets the last update datetime
 *
 * Lifecycle callbacks:
 * - initializeCreatedAt(): Automatically sets createdAt before persisting
 * - initializeUpdatedAt(): Automatically updates updatedAt before updating
 */
trait Timestampable
{
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Automatically initialize createdAt before persisting the entity.
     */
    #[ORM\PrePersist]
    public function initializeCreatedAt(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    /**
     * Automatically update updatedAt before updating the entity.
     */
    #[ORM\PreUpdate]
    public function initializeUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * Get the creation datetime.
     *
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the creation datetime.
     *
     * @param \DateTimeImmutable $dt
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $dt): self
    {
        $this->createdAt = $dt;
        return $this;
    }

    /**
     * Get the last update datetime.
     *
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Set the last update datetime.
     *
     * @param \DateTimeImmutable|null $dt
     * @return self
     */
    public function setUpdatedAt(?\DateTimeImmutable $dt): self
    {
        $this->updatedAt = $dt;
        return $this;
    }
}
