<?php
namespace itsoneiota\eventpublisher\transporter;
use Aws\Kinesis\KinesisClient;
use itsoneiota\eventpublisher\Event;

class KinesisTransporter implements Transporter {

    protected $config;
    protected $kinesisClient;

    /**
     * KinesisTransporter constructor.
     * @param KinesisClient $kinesisClient
     * @param $config
     */
    public function __construct(KinesisClient $kinesisClient, $config) {
        $this->kinesisClient = $kinesisClient;
        $this->config = $config;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        return(property_exists($this->kinesisClient->putRecord($this->buildKinesisRecord($event)), "data"));
    }

    /**
     * @param Event $event
     * @return array
     */
    public function buildKinesisRecord(Event $event){
        $record = array(
            "StreamName"=>$this->getStreamName(),
            "Data"=>json_encode($event),
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