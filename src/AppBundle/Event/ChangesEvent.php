<?php
namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ChangesEvent extends Event
{
    private $changes = array();

    public function __construct(array $changes)
    {
        $this->changes = $changes;
    }

    public function getChanges()
    {
        return $this->changes;
    }
}