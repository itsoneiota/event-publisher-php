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
        $qurl = $this->getQueueURL();
        $result = $this->sqsClient->sendMessage(array(
            'QueueUrl'    => $qurl,
            'MessageBody' => $event->encode(),
        ));
        return(property_exists($result, "data"));
    }


    /**
     *
     * @return string
     * @throws \Exception
     */
    public function getQueueURL() {
        return sprintf("%s/queue/%s", $this->getHostURL(), $this->getQueueName());
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