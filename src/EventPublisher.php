<?php
namespace itsoneiota\eventpublisher;
use itsoneiota\eventpublisher\transporter\Transporter;

class EventPublisher {

    protected $transporter;
    protected $enabled;

    /**
     * @return Transporter
     */
    public function getTransporter() {
        return $this->transporter;
    }

    /**
     * @param Transporter $transporter
     */
    public function setTransporter(Transporter $transporter) {
        $this->transporter = $transporter;
    }

    /**
     * @return boolean
     */
    public function getEnabled() {
        return($this->enabled);
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        if(!$this->enabled) {
            return(TRUE);
        }
        return($this->getTransporter()->publish($event));
    }

}