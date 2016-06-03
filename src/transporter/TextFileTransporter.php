<?php

namespace itsoneiota\eventpublisher\transporter;

use itsoneiota\eventpublisher\Event;

class TextFileTransporter extends AbstractTransporter {

    protected $type = "TextFile";

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getFileLocation() {
        if(!property_exists($this->config, "fileLocation")) {
            throw new \Exception("Text file location required");
        }
        return($this->config->fileLocation);
    }

    /**
     * @return mixed
     */
    public function getPeriodicallyDelete() {
        return $this->periodicallyDelete;
    }

    /**
     * @param mixed $periodicallyDelete
     */
    public function setPeriodicallyDelete($periodicallyDelete) {
        $this->periodicallyDelete = $periodicallyDelete;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        if(file_exists($this->getFileLocation()) && $this->config->periodicallyDelete===true) {
            if (filemtime($this->getFileLocation()) < time() - 3600) {
                unlink($this->getFileLocation());
            }
        }
        if(file_put_contents($this->getFileLocation(), $event->encode() . PHP_EOL, FILE_APPEND) != false) {
            parent::publish($event);
            return(true);
        }
        else {
            return(false);
        }
    }

}