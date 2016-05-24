<?php

namespace itsoneiota\eventpublisher\transporter;
use itsoneiota\eventpublisher\Event;

class MockTransporter implements Transporter {

    protected $config;
    const TYPE = "Mock";

    /**
     * MockTransporter constructor.
     * @param $config
     */
    public function __construct($config=null) {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getType() {
        return self::TYPE;
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