<?php
namespace itsoneiota\eventpublisher;
use Aws\Firehose\FirehoseClient;
use \Aws\Kinesis\KinesisClient;
use Aws\Sqs\SqsClient;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use itsoneiota\eventpublisher\transporter\ElasticSearchTransporter;
use itsoneiota\eventpublisher\transporter\FirehoseTransporter;
use itsoneiota\eventpublisher\transporter\KinesisTransporter;
use itsoneiota\eventpublisher\transporter\MockTransporter;
use itsoneiota\eventpublisher\transporter\SQSTransporter;
use itsoneiota\eventpublisher\transporter\TextFileTransporter;
use itsoneiota\eventpublisher\transporter\RabbitMQTransporter;

class EventPublisherBuilder {

    protected $transporters=array();
    protected $config='';

    /**
     * @return EventPublisherBuilder
     */
    public static function create(){
        return new EventPublisherBuilder();
    }

    /**
     * @param null $config
     * @return EventPublisherBuilder
     */
    public function withMockTransporter($config=null) {
        $this->transporters[] = new MockTransporter($config);
        return($this);
    }

    /**
     * @param KinesisClient $kinesisClient
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withKinesisTransporter(KinesisClient $kinesisClient, $config) {
        $this->transporters[] = new KinesisTransporter($kinesisClient, $config);
        return($this);
    }

    /**
     * @param FirehoseClient $firehoseClient
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withFirehoseTransporter(FirehoseClient $firehoseClient, $config) {
        $this->transporters[] = new FirehoseTransporter($firehoseClient, $config);
        return($this);
    }

    /**
     * @param SqsClient $sqsClient
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withSQSTransporter(SqsClient $sqsClient, $config) {
        $this->transporters[] = new SQSTransporter($sqsClient, $config);
        return($this);
    }

    /**
     * @param AMQPStreamConnection $AMQPClient
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withAMQPTransporter(AMQPStreamConnection $AMQPClient, $config) {
        $this->transporters[] = new RabbitMQTransporter($AMQPClient, $config);
        return($this);
    }

    /**
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withTextFileTransporter($config) {
        $this->transporters[] = new TextFileTransporter($config);
        return($this);
    }

    public function withElasticSearchTransporter($config) {
        $this->transporters[] = new ElasticSearchTransporter($config);
        return($this);
    }

    /**
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withConfig($config) {
        $this->config = $config;
        return($this);
    }

    /**
     * @param EventPublisher $ep
     */
    public function configureEventPublisher(EventPublisher &$ep) {
        $configs = json_decode(json_encode($this->config), true);
        if(!empty($configs) && count($configs)) {
            foreach($configs as $config=>$value) {
                $setter = 'set'.ucfirst($config);
                if(method_exists($ep, $setter)) {
                    $ep->$setter($value);
                }
            }
        }
    }

    /**
     * @return EventPublisher
     * @throws \Exception
     */
    public function build() {
        if(empty($this->transporters)) {
            throw new \Exception("No Transporter Set");
        }
        $ep = new EventPublisher();
        foreach($this->transporters as $transporter) {
            $ep->addTransporter($transporter);
        }
        $this->configureEventPublisher($ep);
        return($ep);
    }
}