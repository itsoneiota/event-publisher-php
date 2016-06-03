<?php
namespace itsoneiota\eventpublisher\metrics;

interface MetricPublisher {

    /**
     *
     * Increment metric value
     *
     * @param $metric
     * @param $value
     * @return mixed
     */
    public function inc($metric, $value=1);


}