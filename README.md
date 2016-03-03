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

    $publisherConfig = new \stdClass();
    $publisherConfig->EventsPublisher = new \stdClass();
    $publisherConfig->EventsPublisher->transport = new \stdClass();

    $publisherConfig->EventsPublisher->enabled = true;
    $publisherConfig->EventsPublisher->transport->type = 'Kinesis';
    $publisherConfig->EventsPublisher->transport->stream = 'events-stream';

    $eventPublisherBuilder = EventPublisherBuilder::create()->withConfig($publisherConfig);

    if($publisherConfig->transport->type == "Kinesis") {
        $eventPublisherBuilder->withKinesisTransporter($this->createKinesisClient(), $publisherConfig->transport);
    }

    $eventPublisher = $eventPublisherBuilder->build();

####Create Event Object

    // Constructor1 is the event origin.
    // Constructor2 is the message type, this should be a constant event type within the service.
    // Constructor3 is an array of fields which can be defined in any way. consider which properties is relevant to the event.

    const SERVICE_NAME = "WebFrontEnd";
    const EVENT_USER_LOGGED_IN = "UserLoggedIn";

    $event = new \itsoneiota\eventpublisher\Event(self::SERVICE_NAME, self::EVENT_USER_LOGGED_IN, array("some message"=>"user logged in after 3 attempts","attempts remaining"=>3));

####Publish the Event

    $eventPublisher->publish($event);

