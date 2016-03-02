<?php
namespace itsoneiota\eventpublisher;

class Event {

    public $header;
    public $body;

    public function __construct($type, array $data) {
        $this->header = new \stdClass();
        $this->header->type= $type;
        $this->header->timeStamp = (new \DateTime())->format('Y-m-d H.i.s');
        $this->body = $data;
    }

    public function getType() {
        if(is_null($this->header)) {
            return(null);
        }
        return($this->header->type);
    }

}