<?php

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
        $this->assertTrue($eventPublisher->publish($event));
    }

    /**
     * can create a Text File Transporter Publisher, and publish event
     * @test
     */
    public function canCreateATextFileTransporterPublisher() {
        $config = new stdClass();
        $config->enabled = true;
        $config->transport = new stdClass();
        $config->transport->fileLocation = "tests/Events.txt";
        $eventPublisher = \itsoneiota\eventpublisher\EventPublisherBuilder::create()
                                                        ->withTextFileTransporter($config->transport)
                                                        ->withConfig($config)
                                                        ->build();

        $this->assertTrue(get_class($eventPublisher)=="itsoneiota\\eventpublisher\\EventPublisher");
        $this->assertTrue($eventPublisher->getEnabled());
        $event = new \itsoneiota\eventpublisher\Event("UnitTestOrigin","TestEventType", array("testKey"=>"testEvent"));
        $this->assertTrue($eventPublisher->publish($event));
    }
}