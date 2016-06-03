<?php

namespace itsoneiota\eventpublisher\metrics;


class AbstractMetricPublisher implements MetricPublisher {

    protected $source;
    protected $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function inc($metric, $value=1){}
}