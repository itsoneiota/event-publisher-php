<?php
use Aws\Kinesis\KinesisClient;
use Aws\Sqs\SqsClient;

class EventPublisherTest extends \PHPUnit_Framework_TestCase {

    public function setUp() {}

    /**
     * can create enabled publisher from builder
     * @test
     */
    public function canCreateEnabledPublisherFromBuilder() {
        $config = new stdClass();
        $config->enabled = true;
        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()->withMockTransporter()->withConfig($config)->build();
        $this->assertTrue(get_class($eventPublisher)=="itsoneiota\\eventpublisher\\EventPublisher");
        $this->assertTrue($eventPublisher->getEnabled());
    }

    /**
     * can create disabled publisher from builder
     * @test
     */
    public function canCreateDisabledPublisherFromBuilder() {
        $config = new stdClass();
        $config->enabled = false;
        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()->withMockTransporter()->withConfig($config)->build();
        $this->assertTrue(get_class($eventPublisher)=="itsoneiota\\eventpublisher\\EventPublisher");
        $this->assertFalse($eventPublisher->getEnabled());
    }

    /**
     * can create enabled publisher from builder, and publish event
     * @test
     */
    public function canCreateADisabledEventPublisherBuilder() {
        $config = new stdClass();
        $config->enabled = true;
        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()->withMockTransporter()->withConfig($config)->build();
        $this->assertTrue(get_class($eventPublisher)=="itsoneiota\\eventpublisher\\EventPublisher");
        $this->assertTrue($eventPublisher->getEnabled());
        $event = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","TestEventType", array("testKey"=>"testEvent"));
        $result = $eventPublisher->publish($event);
        $this->assertTrue($this->assesResult($result));
    }

    /**
     * can create a Text File Transporter Publisher, and publish event
     * @test
     */
    public function canCreateATextFileTransporterPublisher() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;
        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
                                                        ->withTextFileTransporter($config->transport)
                                                        ->withConfig($config)
                                                        ->build();

        $this->assertTrue(get_class($eventPublisher)=="itsoneiota\\eventpublisher\\EventPublisher");
        $this->assertTrue($eventPublisher->getEnabled());
        $event = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","TestEventType", array("testKey"=>"testEvent"));
        $result = $eventPublisher->publish($event);
        $this->assertTrue($this->assesResult($result));
    }

    /**
     *
     * This provides the ability to only allow the publication of provided events.
     * @test
     * 
     */
    public function canRestrictEventPublishingByType() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;
        $config->transport->acceptableEvents = array('Acceptable');
        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
            ->withTextFileTransporter($config->transport)
            ->withConfig($config)
            ->build();

        $acceptableEvent = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","Acceptable", array("testKey"=>"testEvent"));
        $nonAcceptableEvent = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","NotAcceptable", array("testKey"=>"testEvent"));
        $transporter = $eventPublisher->getTransporters()[0];
        $this->assertTrue($transporter->canPublish($acceptableEvent));
        $this->assertFalse($transporter->canPublish($nonAcceptableEvent));
    }

    /**
     *
     * This provides the ability to only allow publication of all events if restriction not specified.
     * @test
     *
     */
    public function canPublishAllEventsIfRestrictionNotSpecified() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;
        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
            ->withTextFileTransporter($config->transport)
            ->withConfig($config)
            ->build();

        $acceptableEvent = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","Acceptable", array("testKey"=>"testEvent"));
        $nonAcceptableEvent = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","NotAcceptable", array("testKey"=>"testEvent"));
        $transporter = $eventPublisher->getTransporters()[0];
        $this->assertTrue($transporter->canPublish($acceptableEvent));
        $this->assertTrue($transporter->canPublish($nonAcceptableEvent));
    }

    /**
     *
     * This provides the ability to specify multiple transporters.
     * @test
     *
     */
    public function canCreateAPublisherWithMultipleTransporters() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;

        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
            ->withTextFileTransporter($config->transport)
            ->withConfig($config)
            ->build();

        $textTransport = new stdClass();
        $textTransport->type = "Text";
        $textTransport->fileLocation = "tests/Events.txt";
        $textTransport->periodicallyDelete = true;

        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
                                                        ->withElasticSearchTransporter($textTransport)
                                                        ->withMockTransporter()
                                                        ->withConfig($config)
                                                        ->build();
        $totalTransporters = count($eventPublisher->getTransporters());
        $this->assertEquals(2, $totalTransporters);
    }

    /**
     *
     * This provides the ability to provide a metric component.
     * @test
     *
     */
    public function canCreateAPublisherWithAMetricsPublisher() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;
        $config->transport->metrics = new stdClass();
        $config->transport->metrics->enabled = true;
        $config->transport->metrics->type = "mock";

        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
                                                            ->withTextFileTransporter($config->transport)
                                                            ->withConfig($config)
                                                            ->build();

        $transporter = $eventPublisher->getTransporters()[0];
        $this->assertTrue($transporter->enabledForMetrics());
    }

    /**
     *
     * Tests the mock metrics publisher
     * @test
     *
     */
    public function canCreateAPublisherWithAMockMetricsPublisher() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;
        $config->transport->metrics = new stdClass();
        $config->transport->metrics->enabled = true;
        $config->transport->metrics->type = "mock";

        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
            ->withTextFileTransporter($config->transport)
            ->withConfig($config)
            ->build();

        $transporter = $eventPublisher->getTransporters()[0];
        $this->assertTrue($transporter->enabledForMetrics());

        $event = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","TestEventType", array("testKey"=>"testEvent"));
        $result = $eventPublisher->publish($event);
        $this->assertTrue($this->assesResult($result));
    }

    /**
     *
     * This provides the ability to not provide a metric component.
     *
     * Checks enable toggle, and if no config provided.
     *
     * @test
     *
     */
    public function canCreateAPublisherWithoutAMetricsPublisher() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;
        $config->transport->metrics = new stdClass();
        $config->transport->metrics->enabled = false;
        $config->transport->metrics->source = "TESTHOST";
        $config->transport->metrics->type = "statsd";
        $config->transport->metrics->host = "http://localhost";

        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
            ->withTextFileTransporter($config->transport)
            ->withConfig($config)
            ->build();

        $transporter = $eventPublisher->getTransporters()[0];

        $this->assertFalse($transporter->enabledForMetrics());

        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->type = "Text";
        $config->transport->fileLocation = "tests/Events.txt";
        $config->transport->periodicallyDelete = true;

        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
            ->withTextFileTransporter($config->transport)
            ->withConfig($config)
            ->build();

        $transporter = $eventPublisher->getTransporters()[0];

        $this->assertFalse($transporter->enabledForMetrics());

    }

    /**
     *
     * Needs ES running!
     *
     * can Create An ElasticSearch Transporter Publisher, and publish event
     * @test
     */
    public function canCreateAnElasticSearchTransporterPublisher() {
//        $config = new stdClass();
//        $config->enabled = true;
//        $config->transport = new stdClass();
//        $config->transport->type = "ElasticSearch";
//        $config->transport->host = "localhost";
//        $config->transport->port = "9200";
//        $config->transport->index = "logs";
//
//        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
//                                                        ->withElasticSearchTransporter($config->transport)
//                                                        ->withConfig($config)
//                                                        ->build();
//        $this->assertTrue(get_class($eventPublisher)=="itsoneiota\\eventpublisher\\EventPublisher");
//        $this->assertTrue($eventPublisher->getEnabled());
//        $event = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","TestEventType", array("testKey"=>"testEvent"));
//        $result = $eventPublisher->publish($event);
//        $this->assertTrue($this->assesResult($result));
    }

    /**
     * Testing multiple transporters. Needs sqs and kinesis running!
     *
     * @test
     */
    public function canCreateAKinesisAndSQSTransporterPublisher() {
//        $config = new stdClass();
//        $config->enabled = true;
//        $config->transport = array();
//
//        $ktransport = new stdClass();
//        $ktransport->type = "Kinesis";
//        $ktransport->host = "http://localhost:4567";
//        $ktransport->stream = "event-queue";
//        $ktransport->partitions = 1;
//
//        $sqsTransport = new stdClass();
//        $sqsTransport->type = "SQS";
//        $sqsTransport->host = "http://localhost:9324";
//        $sqsTransport->queueName = "workflow";
//        $sqsTransport->queueURL = "http://localhost:9324/queue/workflow";
//
//        $config->transport["Kinesis"] = $ktransport;
//        $config->transport["SQS"] = $sqsTransport;
//
//        $kinesis = $this->createKinesisClient($ktransport->host);
//        $sqs = $this->createSQSClient($sqsTransport->host);
//        try {
//            $kinesis->createStream(array('ShardCount'=>$ktransport->partitions, 'StreamName'=>$ktransport->stream));
//        }
//        catch(Exception $ex) {}
//        try {
//            $sqs->createQueue(array('QueueName' => $sqsTransport->queueName));
//        }
//        catch(Exception $ex) {}
//
//        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
//            ->withKinesisTransporter($kinesis, $config->transport["Kinesis"])
//            ->withSQSTransporter($sqs, $config->transport["SQS"])
//            ->withConfig($config)
//            ->build();
//
//        $this->assertTrue(get_class($eventPublisher)=="itsoneiota\\eventpublisher\\EventPublisher");
//        $this->assertTrue($eventPublisher->getEnabled());
//        $event = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","TestEventType", array("testKey"=>"testEvent"));
//        $result = $eventPublisher->publish($event);
//        $this->assertTrue($this->assesResult($result));
    }

    protected function assesResult($result) {
        foreach($result as $res) {
            if($res['Success']!="True") {
                return false;
            }
        }
        return true;
    }

    /**
     * @return KinesisClient
     */
    protected function createKinesisClient($host) {
        $kinesis = KinesisClient::factory(array(
                "region"=> "eu-west-1",
                "endpoint"=> $host
            )
        );
        $kinesis->setBaseUrl($host);
        return($kinesis);
    }

    /**
     * @return SqsClient
     */
    protected function createSQSClient($host) {
        $sqs = SqsClient::factory(array(
                "region"=> "eu-west-1",
                "endpoint"=> $host
            )
        );
        $sqs->setBaseUrl($host);
        return($sqs);
    }

}