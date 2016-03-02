<?php

namespace itsoneiota\eventpublisher\transporter;
use itsoneiota\eventpublisher\Event;

class MockTransporter {

    protected $config;

    /**
     * MockTransporter constructor.
     * @param $config
     */
    public function __construct($config=null) {
        $this->config = $config;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        if(!is_null($event->getType())) {
            return(true);
        }
        return(false);
    }

}