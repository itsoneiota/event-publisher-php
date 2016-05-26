<?php

namespace itsoneiota\eventpublisher\transporter;

use itsoneiota\eventpublisher\Event;

class AbstractTransporter implements Transporter {

    protected $config;
    protected $type = "Abstract";

    public function publish(Event $event) {}

    public function canPublish(Event $event) {
        $canPublish=true;
        if(!is_null($this->config)) {
            if(property_exists($this->config, 'acceptableEvents') && is_array($this->config->acceptableEvents)) {
                $canPublish=false;
                if(in_array($event->getType(), $this->config->acceptableEvents)) {
                    $canPublish=true;
                }
            }
        }
        return($canPublish);
    }

    public function getType() {
        return($this->type);
    }

}