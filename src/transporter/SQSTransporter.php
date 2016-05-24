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
        $result = $this->sqsClient->getQueueUrl(array('QueueName' => $this->getQueueName()));
        $qurl = $result->get('QueueUrl');
        $result = $this->sqsClient->sendMessage(array(
            'QueueUrl'    => $qurl,
            'MessageBody' => $event->encode(),
        ));
        return(property_exists($result, "data"));
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