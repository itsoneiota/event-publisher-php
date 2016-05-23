<?php
namespace itsoneiota\eventpublisher;
use itsoneiota\eventpublisher\transporter\Transporter;

class EventPublisher {

    protected $transporters=array();
    protected $enabled;

    /**
     * @return Transporter
     */
    public function getTransporters() {
        return $this->transporters;
    }

    /**
     * @param Transporter $transporter
     */
    public function addTransporter(Transporter $transporter) {
        $this->transporters[] = $transporter;
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
        $success=true;
        foreach($this->getTransporters() as $transporter) {
            $success=$transporter->publish($event);
            if(!$success) {
                break;
            }
        }
        return($success);
    }

}