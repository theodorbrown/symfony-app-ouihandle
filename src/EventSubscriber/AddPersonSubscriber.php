<?php

namespace App\EventSubscriber;

use App\Events\AddPersonEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddPersonSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public static function getSubscribedEvents(): array
    {
        return [
            AddPersonEvent::ADD_PERSON_EVENT => ['onAddPersonEvent', 3000]
        ];
    }

    public function onAddPersonEvent(AddPersonEvent $evt) {
        $this->logger->debug('Subscriber : A new person has been added to db: '.$evt->getPerson()->getFirstname().' '.$evt->getPerson()->getLastname());
    }
}