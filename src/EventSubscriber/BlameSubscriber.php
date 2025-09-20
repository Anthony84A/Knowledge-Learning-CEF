<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;
use App\Entity\Traits\Blameable;
use App\Entity\User;

/**
 * BlameSubscriber
 *
 * A Doctrine event subscriber that automatically populates `createdBy` and `updatedBy`
 * fields for entities using the Blameable trait.
 *
 * Subscribed events:
 * - prePersist: Sets both createdBy and updatedBy to the currently authenticated user
 * - preUpdate: Updates updatedBy to the currently authenticated user
 */
class BlameSubscriber implements EventSubscriber
{
    private Security $security;

    /**
     * Constructor.
     *
     * @param Security $security Symfony Security service to get the currently authenticated user
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Returns the list of Doctrine events this subscriber is interested in.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * PrePersist event handler.
     * Sets createdBy and updatedBy fields for Blameable entities before persisting.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (in_array(Blameable::class, class_uses($entity))) {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                $entity->setCreatedBy($user);
                $entity->setUpdatedBy($user);
            }
        }
    }

    /**
     * PreUpdate event handler.
     * Updates updatedBy field for Blameable entities before updating.
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (in_array(Blameable::class, class_uses($entity))) {
            $user = $this->security->getUser();
            if ($user instanceof User) {
                $entity->setUpdatedBy($user);
            }
        }
    }
}
