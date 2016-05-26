<?php
namespace itsoneiota\eventpublisher\transporter;
use itsoneiota\eventpublisher\Event;

interface Transporter {

    /**
     * @param Event $event
     * @return mixed
     */
    public function publish(Event $event);
    public function canPublish(Event $event);
    public function getType();

}