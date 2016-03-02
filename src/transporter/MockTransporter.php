<?php

namespace itsoneiota\eventpublisher\transporter;

class MockTransporter {

    protected $memcached;

    public function __construct(\Memcached $memcached, $config) {
        $this->memcached = $memcached;
        $this->config = $config;
    }
}