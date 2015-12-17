<?php
namespace Libs\AMQ;
/**
 * @author Thaer AlDwaik <t_dwaik@hotmail.com>
 * @since November 18, 2015
 *
 */

/**
 * Class ActiveMqMessage
 */
class ActiveMqMessage {

    /**
     * @var string
     */
    private $body = null;

    /**
     * @var string
     */
    private $destination = null;

    /**
     * @var string
     */
    private $type = null;

    /**
     * Value from 0-9
     * @var int
     */
    private $priority = null;

    /**
     * @var bool
     */
    private $persistent = null;

    /**
     * @var int
     */
    private $expiration = null;

    /**
     * @var null
     */
    private $stomp = null;

    /**
     * @var array
     */
    private $headers = array();

    /**
     * @param $stomp
     */
    public function __construct($stomp) {
        $this->stomp = $stomp;
    }

    /**
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param $body
     * @return $this
     */
    public function body($body) {
        $this->body = $body;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestination() {
        return $this->destination;
    }

    /**
     * @param $destination
     * @return $this
     */
    public function destination($destination) {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function type($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * @param $priority
     * @return $this
     */
    public function priority($priority) {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return bool
     */
    public function getPersistent() {
        return $this->persistent;
    }

    /**
     * @param $persistent
     * @return $this
     * @throws Exception
     */
    public function persistent($persistent) {
        if($persistent == true)
            $this->persistent = 'true';
        elseif ($persistent == false) {
            $this->persistent = 'false';
        }else {
            throw new Exception("Error: Wrong Persistent value, only (true, false) accepted");
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiration() {
        return $this->expiration;
    }

    /**
     * @param $expiration
     * @return mixed
     */
    public function expiration($expiration) {
        return $this->expiration = $expiration;
    }

    /**
     * @return null
     */
    public function getStomp() {
        return $this->stomp;
    }

    /**
     * @param $stomp
     * @return $this
     */
    public function stomp($stomp) {
        $this->stomp = $stomp;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param $headers
     * @return $this
     */
    public function headers($headers) {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function send() {
        $destination = $this->destination;
        if(empty($destination)) {
            throw new Exception('Queue destination empty');
        }
        
        try {
            return $this->stomp->send($destination, $this->body, $this->_getHeaders());
        }catch(Exception $e) {
            throw $e;
        }
    }

    /**
     * @return array
     */
    private function _getHeaders() {
        if(!empty($this->headers)) {
            return $this->headers;
        }

        $headers = array();

        if($this->persistent !== null) {
            $headers['persistent'] = $this->persistent;
        }

        if($this->priority !== null) {
            $headers['priority'] = $this->priority;
        }

        if($this->type !== null) {
            $headers['type'] = $this->type;
        }

        return $headers;
    }

}