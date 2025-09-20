<?php

namespace App\Entity;

use App\Repository\LessonValidationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Blameable;

/**
 * LessonValidation entity
 *
 * Represents the validation status of a lesson for a specific user.
 *
 * Uses:
 * - Timestampable: Tracks creation and update times
 * - Blameable: Tracks which user created or updated the entity
 *
 * Relationships:
 * - User: Many-to-one relationship
 * - Lesson: Many-to-one relationship
 */
#[ORM\Entity(repositoryClass: LessonValidationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LessonValidation
{
    use Timestampable;
    use Blameable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isValidated = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $validatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'lessonValidations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'lessonValidations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lesson $lesson = null;

    // --- Getters and setters ---

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Check if the lesson has been validated by the user.
     *
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    /**
     * Set the validation status of the lesson.
     * Automatically sets validatedAt if validating.
     *
     * @param bool $isValidated
     * @return static
     */
    public function setIsValidated(bool $isValidated): static
    {
        $this->isValidated = $isValidated;

        if ($isValidated && $this->validatedAt === null) {
            $this->validatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Get the datetime when the lesson was validated.
     *
     * @return \DateTimeImmutable|null
     */
    public function getValidatedAt(): ?\DateTimeImmutable
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?\DateTimeImmutable $validatedAt): static
    {
        $this->validatedAt = $validatedAt;
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
}
