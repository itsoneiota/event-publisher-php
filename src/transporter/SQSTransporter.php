<?php
namespace itsoneiota\eventpublisher\transporter;


use Aws\Sqs\SqsClient;
use itsoneiota\eventpublisher\Event;

class SQSTransporter implements Transporter {

    protected $sqsClient;
    protected $config;

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
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        $result = $this->sqsClient->sendMessage(array(
            'QueueUrl'    => $this->getQueueURL(),
            'MessageBody' => $event->encode(),
        ));
        return(property_exists($result, "data"));
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getQueueURL() {
        if(!property_exists($this->config, "queueURL")) {
            return sprintf("%s/queue/%s", $this->getHostURL(), $this->getQueueName());
        }
        return($this->config->queueURL);
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