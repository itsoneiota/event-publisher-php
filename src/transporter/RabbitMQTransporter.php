<?php
namespace itsoneiota\eventpublisher\transporter;
use itsoneiota\eventpublisher\Event;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQTransporter extends AbstractTransporter {

    protected $rmqConnection;
    protected $type = "AMQP";
    protected $topic = "events";
    protected $exchangeType = "topic";

    /**
     * SQSTransporter constructor.
     * @param AMQPStreamConnection $rmqConnection
     * @param $config
     */
    public function __construct(AMQPStreamConnection $rmqConnection, $config) {
        $this->rmqConnection = $rmqConnection;
        parent::__construct($config);
    }

    /**
     * @param Event $event
     * @return bool
     */
    public function publish(Event $event) {
        $channel = $this->rmqConnection->channel();
        $channel->exchange_declare(
            $this->topic, //Topic name
            $this->exchangeType, //Exchange type is 'topic'
            false, //Passive - False to create the exchange if it does'nt exist.
            true, //Durable - Make sure messages persisted.
            false //Auto-delete - delete the exchange if no connections are made.
        );

        if(!$routingKey = $this->getRoutingKey($event)) {
            return(false);
        }

        $msg = new AMQPMessage($event->encode());

        $channel->basic_publish($msg, $this->topic, $routingKey);
        $channel->close();
        $this->rmqConnection->close();

        parent::publish($event);
        return(true);
    }

    /**
     * @param Event $event
     * @return bool|string
     */
    protected function getRoutingKey(Event $event) {
        if(empty($event->getOrigin()) || $event->getType()) {
            return(false);
        }
        return sprintf("%s.%s.%s", $event->getType(), $event->getOrigin(), $event->getDomain());
    }

}