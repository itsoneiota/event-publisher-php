One iota Event Publisher
========================

Overview
--------
A PHP event publisher which is responsible for publishing user defined events within a multi service environment


Installation
------------
    composer require itsoneiota/event-publisher

Testing
-------
	./vendor/bin/phpunit

Basic Usage
-----------

### Builder
There is a builder helper class to build your publisher.

### Build Kinesis Publisher
A publisher with an AWS Kinesis transporter requires a Kinesis Client from the AWS SDK to build.

e.g.

###Example usage - Event publishing with a Kinesis Client

####Create Kinesis Client

    $kinesis = new Aws\Kinesis\KinesisClient::factory(array('key'=>'KeY','secret'=>'seCrEt','region'=>'eu-west-1');

####Create Publisher using builder

    $config = new \stdClass();
    $config->EventsPublisher = new \stdClass();
    $config->EventsPublisher->transport = new \stdClass();

    $config->EventsPublisher->enabled = true;
    $config->EventsPublisher->transport->type = 'kinesis';
    $config->EventsPublisher->transport->stream = 'events-stream';

    $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
                                                                            ->withConfig($config->EventsPublisher)
                                                                            ->withKinesisTransporter($kinesis, $config->EventsPublisher->transport)
                                                                            ->build();

####Create Event Object

    // Constructor1 is the message type, this should be a constant event type within the service. Namespacing should be considered.
    // Constructor2 is an array of fields which can be defined in any way.

    const SERVICE_NAME = "WebFrontEnd";
    const EVENT_USER_LOGGED_IN = "UserLoggedIn";

    $event = new \itsoneiota\eventpublisher\Event(self::SERVICE_NAME."/".self::EVENT_USER_LOGGED_IN, array("some message"=>"user logged in after 3 attempts","attempts remaining"=>3));

####Publish the Event

    $eventPublisher->publish($event);

    