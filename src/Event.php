<?php
namespace itsoneiota\eventpublisher;

class Event {

    protected $origin="UNDEFINED";
    protected $domain="all";
    protected $traceID=null;

    protected $type;
    protected $data;
    protected $timestamp;

    /**
     * Event constructor.
     * @param null $origin
     * @param null $type
     * @param array|null $data
     */
    public function __construct($origin=null, $type=null, array $data=null) {
        $this->origin = $origin;
        $this->type = $type;
        $this->data = $data;
        $this->timestamp = round(microtime(true) * 1000);
    }

    public function encode() {
        $event = new \stdClass();
        $event->header = new \stdClass();
        $event->header->origin = $this->origin;
        $event->header->type = $this->type;
        $event->header->timeStamp = $this->timestamp;
        $event->header->traceID = $this->traceID;
        $event->body = $this->data;
        return(json_encode($event));
    }

    /**
     * @return mixed
     */
    public function getTraceID() {
        return $this->traceID;
    }

    /**
     * @param mixed $traceID
     */
    public function setTraceID($traceID) {
        $this->traceID = $traceID;
    }

    /**
     * @return null|string
     */
    public function getOrigin() {
        return($this->origin);
    }

    /**
     * @param $origin
     * @return Event
     */
    public function setOrigin($origin) {
        $this->origin = $origin;
        return($this);
    }

    /**
     * @param null $type
     * @return Event
     */
    public function setType($type) {
        $this->type = $type;
        return($this);
    }

    /**
     * @return mixed
     */
    public function getType() {
        return($this->type);
    }

    /**
     * @return array|null
     */
    public function getData() {
        return($this->data);
    }

    /**
     * @param $data
     * @return Event
     */
    public function setData($data) {
        $this->data = $data;
        return($this);
    }

    /**
     * @return mixed
     */
    public function getDomain() {
        return($this->domain);
    }

    /**
     * Set domain of event e.g. store name
     *
     * Default = "all"
     *
     * @param mixed $domain
     */
    public function setDomain($domain) {
        $this->domain = $domain;
    }

}