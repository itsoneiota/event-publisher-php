<?php

namespace itsoneiota\eventpublisher\metrics;
use PhpStatsD\StatsD;

class StatsdMetricPublisher extends AbstractMetricPublisher {

    protected $client;
    const DEFAULT_HOST="127.0.0.1";
    const DEFAULT_PORT=8125;

    /**
     * StatsdMetricPublisher constructor.
     * @param $config
     */
    public function __construct($config) {
        parent::__construct($config);
        $this->createClient();
    }

    /**
     * Creates a statsd client.
     */
    protected function createClient() {
        $port=self::DEFAULT_PORT;
        $host=self::DEFAULT_HOST;
        if(property_exists($this->config, "host")) {
            $host = $this->config->host == null ? "127.0.0.1" : $this->config->host;
        }
        if(property_exists($this->config, "port")) {
            $port = $this->config->port == null ? 8125 : (int)$this->config->port;
        }
        $this->client = new StatsD($host, $port);
    }

    /**
     * @param $metric
     * @param int $value
     * @return void
     */
    public function inc($metric, $value=1){
        $this->client->counting($metric, $value);
    }
}