<?php
namespace itsoneiota\eventpublisher\metrics;

class MockMetricPublisher extends AbstractMetricPublisher {

    protected $metricMap=array();

    public function inc($metric, $value=1){
        if(!array_key_exists($metric, $this->metricMap)) {
            $this->metricMap[$metric]=0;
        }
        $this->metricMap[$metric]+=$value;
    }

    public function getMetricValue($metric) {
        if(!array_key_exists($metric, $this->metricMap)) {
            return(0);
        }
        return($this->metricMap[$metric]);
    }
}