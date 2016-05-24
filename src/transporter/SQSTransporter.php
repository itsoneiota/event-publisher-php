<?php
namespace itsoneiota\eventpublisher\transporter;


use Aws\Sqs\SqsClient;
use itsoneiota\eventpublisher\Event;

class SQSTransporter implements Transporter {

    protected $sqsClient;
    protected $config;
    const TYPE = "SQS";

    /**
     * SQSTransporter constructor.
     * @param SqsClient $kinesisClient
     * @param $config
     */
    public function __construct(SqsClient $kinesisClient, $config) {
        $this->sqsClient = $kinesisClient;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getType() {
        return self::TYPE;
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        $result = $this->sqsClient->sendMessage(array(
            'QueueUrl'    => $this->getQueueURL($this->getQueueName()),
            'MessageBody' => $event->encode(),
        ));
        return(property_exists($result, "data"));
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getQueueURL($qname) {
        $qurl = null;
        try {
            $result = $this->sqsClient->getQueueUrl(array('QueueName' => $qname));
            $qurl = $result->get('QueueUrl');
            return($qurl);
        }
        catch(\Exception $ex) {}
        if(is_null($qurl)) {
            if($this->getCreateQueueIfNotExist()) {
                $this->sqsClient->createQueue(array('QueueName' => $qname));
                return($this->getQueueURL($qname));
            }
        }
        return($this->config->queueURL);
    }

    /**
     * @return bool
     */
    protected function getCreateQueueIfNotExist() {
        if(!property_exists($this->config, "createQueue")) {
           return(false);
        }
        return(true);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getHostURL() {
        if(!property_exists($this->config, "host")) {
            throw new \Exception("SQS Hostname required");
        }
        return($this->config->host);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getQueueName() {
        if(!property_exists($this->config, "queueName")) {
            throw new \Exception("SQS Queue Name required");
        }
        return($this->config->queueName);
    }


}