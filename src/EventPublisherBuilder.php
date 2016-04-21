<?php
namespace itsoneiota\eventpublisher;
use Aws\Firehose\FirehoseClient;
use \Aws\Kinesis\KinesisClient;
use itsoneiota\eventpublisher\transporter\ElasticSearchTransporter;
use itsoneiota\eventpublisher\transporter\FirehoseTransporter;
use itsoneiota\eventpublisher\transporter\KinesisTransporter;
use itsoneiota\eventpublisher\transporter\MockTransporter;
use itsoneiota\eventpublisher\transporter\TextFileTransporter;

class EventPublisherBuilder {

    protected $transporter=NULL;
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
        $this->transporter = new MockTransporter($config);
        return($this);
    }

    /**
     * @param KinesisClient $kinesisClient
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withKinesisTransporter(KinesisClient $kinesisClient, $config) {
        $this->transporter = new KinesisTransporter($kinesisClient, $config);
        return($this);
    }

    /**
     * @param FirehoseClient $firehoseClient
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withFirehoseTransporter(FirehoseClient $firehoseClient, $config) {
        $this->transporter = new FirehoseTransporter($firehoseClient, $config);
        return($this);
    }

    /**
     * @param $config
     * @return EventPublisherBuilder
     */
    public function withTextFileTransporter($config) {
        $this->transporter = new TextFileTransporter($config);
        return($this);
    }

    public function withElasticSearchTransporter($config) {
        $this->transporter = new ElasticSearchTransporter($config);
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
        if(count($configs)) {
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
        if(is_null($this->transporter)) {
            throw new \Exception("No Transporter Set");
        }
        $ep = new EventPublisher($this->config);
        $ep->setTransporter($this->transporter);
        $this->configureEventPublisher($ep);
        return($ep);
    }


}