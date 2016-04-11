<?php

namespace itsoneiota\eventpublisher\transporter;

use itsoneiota\eventpublisher\Event;

class ElasticSearchTransporter implements Transporter {

    protected $config;

    /**
     * ElasticSearchTransporter constructor.
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getHost() {
        if(!property_exists($this->config, "host")) {
            throw new \Exception("Kibana Host is required");
        }
        return($this->config->host);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getPort() {
        if(!property_exists($this->config, "port")) {
            throw new \Exception("Kibana Port is required");
        }
        return($this->config->port);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getIndex() {
        if(!property_exists($this->config, "index")) {
            throw new \Exception("Kibana Index is required");
        }
        return($this->config->index);
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        $ep = $this->getHost().":".$this->getPort()."/".$this->getIndex()."/".$event->getType();
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, $ep);
        curl_setopt($tuCurl, CURLOPT_VERBOSE, false);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $event->encode());
        $tuData = curl_exec($tuCurl);
        if(!curl_errno($tuCurl)){
            return(true);
        } else {
            return(false);
        }
    }

}