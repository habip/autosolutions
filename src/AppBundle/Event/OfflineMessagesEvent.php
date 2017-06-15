<?php
namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class OfflineMessagesEvent extends Event
{
    private $messages = array();

    public function __construct($messages)
    {
        $this->messages = $messages;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}