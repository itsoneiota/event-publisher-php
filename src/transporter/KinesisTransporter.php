<?php
namespace itsoneiota\eventpublisher\transporter;
use Aws\Kinesis\KinesisClient;
use itsoneiota\eventpublisher\Event;

class KinesisTransporter extends AbstractTransporter {

    protected $kinesisClient;
    protected $type = "Kinesis";

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
            "Data"=>$event->encode(),
            "PartitionKey"=>$this->getPartitionKey()
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

    /**
     * @return int
     * @throws \Exception
     */
    protected function getPartitions() {
        $partitions=1;
        if(property_exists($this->config, "partitions")) {
            $partitions=$this->config->partitions;
        }
        return((int)$partitions);
    }

    /**
     *
     * Randomises the stream, needs to be a string
     *
     * @return string
     * @throws \Exception
     */
    protected function getPartitionKey() {
        $partitions = $this->getPartitions();
        if($partitions < 1) {
            $partitions=1;
        }
        if($partitions==1) {
            return((string)$partitions);
        }
        return((string)rand(0,$partitions));
    }
}