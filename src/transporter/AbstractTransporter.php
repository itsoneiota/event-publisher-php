<?php

namespace itsoneiota\eventpublisher\transporter;

use itsoneiota\eventpublisher\Event;
use itsoneiota\eventpublisher\metrics\MockMetricPublisher;
use itsoneiota\eventpublisher\metrics\StatsdMetricPublisher;

class AbstractTransporter implements Transporter {

    protected $config;
    protected $type = "Abstract";
    protected $metricPublisher;

    /**
     * ElasticSearchTransporter constructor.
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;
        $this->init();
    }

    public function publish(Event $event) {
        if($this->enabledForMetrics()) {
            $this->getMetricPublisher()->inc($event->getOrigin().".".$event->getType());
        }
    }

    /**
     * @param Event $event
     * @return bool
     */
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

    /**
     * Initialise the transporter
     *
     */
    protected function init() {
        if(!is_null($this->config)) {
            if(property_exists($this->config, "metrics")) {
                if(property_exists($this->config->metrics, "enabled") && $this->config->metrics->enabled==true) {
                    switch ($this->config->metrics->type) {
                        case "mock":
                            $publisher = new MockMetricPublisher($this->config->metrics);
                            break;
                        case "statsd":
                            $publisher = new StatsdMetricPublisher($this->config->metrics);
                            break;
                        default:
                            $publisher = new MockMetricPublisher($this->config->metrics);
                            break;
                    }
                    $this->metricPublisher = $publisher;
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function enabledForMetrics() {
        return(!is_null($this->metricPublisher));
    }

    /**
     * @return string
     */
    public function getType() {
        return($this->type);
    }

    /**
     * @param mixed $metricPublisher
     * @return AbstractTransporter
     */
    public function setMetricPublisher($metricPublisher) {
        $this->metricPublisher = $metricPublisher;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetricPublisher() {
        return($this->metricPublisher);
    }

}