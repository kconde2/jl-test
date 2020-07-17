<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class UserChangedNotifier
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function postUpdate(User $user, LifecycleEventArgs $event)
    {
        $id = $user->getId();
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();

        $this->logger->info("User with id : $id has changed with firstname : $firstname, lastname : $lastname");
    }
}
