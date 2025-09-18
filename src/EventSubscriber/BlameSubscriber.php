<?php

namespace App\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;
use App\Entity\Traits\Blameable;
use App\Entity\User;

/**
 * Subscriber Doctrine qui remplit automatiquement createdBy et updatedBy
 */
class BlameSubscriber implements EventSubscriber
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Liste des événements Doctrine auxquels ce subscriber s'abonne
     *
     * @return array<string>
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        /** @var Blameable|null $entity */
        if (in_array(Blameable::class, class_uses($entity))) {
            /** @var User|null $user */
            $user = $this->security->getUser();
            if ($user) {
                $entity->setCreatedBy($user);
                $entity->setUpdatedBy($user);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        /** @var Blameable|null $entity */
        if (in_array(Blameable::class, class_uses($entity))) {
            /** @var User|null $user */
            $user = $this->security->getUser();
            if ($user) {
                $entity->setUpdatedBy($user);
            }
        }
    }
}
