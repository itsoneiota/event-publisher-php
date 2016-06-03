<?php

namespace itsoneiota\eventpublisher\transporter;
use itsoneiota\eventpublisher\Event;

class MockTransporter extends AbstractTransporter {

    protected $type = "Mock";

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        if(!is_null($event->getType())) {
            parent::publish($event);
            return(true);
        }
        return(false);
    }

}