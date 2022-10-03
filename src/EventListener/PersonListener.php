<?php

namespace App\EventListener;

use App\Events\AddPersonEvent;
use Psr\Log\LoggerInterface;

//class à l'écoute (activée dans services.yaml)
class PersonListener
{
    public function __construct(private LoggerInterface $logger)
    {}

    public function onAddPersonEvent(AddPersonEvent $evt){
        $this->logger->debug('Listener : A new person has been added to db: '.$evt->getPerson()->getFirstname().' '.$evt->getPerson()->getLastname());
    }

}