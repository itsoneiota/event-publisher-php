<?php

namespace itsoneiota\eventpublisher\transporter;


use Aws\Firehose\FirehoseClient;
use itsoneiota\eventpublisher\Event;

class FirehoseTransporter extends AbstractTransporter {

    protected $firehoseClient;
    protected $type = "FireHose";

    /**
     * KinesisTransporter constructor.
     * @param FirehoseClient $firehoseClient
     * @param $config
     */
    public function __construct(FirehoseClient $firehoseClient, $config) {
        $this->firehoseClient = $firehoseClient;
        parent::__construct($config);
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        if(!property_exists($this->firehoseClient->putRecord($this->buildFirehoseRecord($event)), "data")) {
            return(false);
        }
        parent::publish($event);
        return(true);
    }

    /**
     * @param Event $event
     * @return array
     */
    public function buildFirehoseRecord(Event $event){
        $record = array(
            "DeliveryStreamName"=>$this->getStreamName(),
            "Record"=>[
                "Data"=>$event->encode()
            ],
            "PartitionKey"=>$event->getType()
        );
        return($record);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getStreamName() {
        if(!property_exists($this->config, "stream")) {
            throw new \Exception("Kinesis stream name required");
        }
        return($this->config->stream);
    }

}