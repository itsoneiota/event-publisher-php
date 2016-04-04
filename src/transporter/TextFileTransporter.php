<?php

namespace itsoneiota\eventpublisher\transporter;

use itsoneiota\eventpublisher\Event;

class TextFileTransporter implements Transporter {

    protected $config;
    protected $periodicallyDelete;

    /**
     * TextFileTransporter constructor.
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;
    }

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
        if(file_exists($this->getFileLocation())) {
            if (filemtime($this->getFileLocation()) < time() - 3600) {
                unlink($this->getFileLocation());
            }
        }
        return(file_put_contents($this->getFileLocation(), $event->encode() . PHP_EOL, FILE_APPEND) != false);
    }

}