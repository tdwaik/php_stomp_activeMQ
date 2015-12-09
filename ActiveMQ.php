<?php

/**
 * @author Thaer AlDwaik <t_dwaik@hotmail.com>
 * @since November 18, 2015
 *
 */

/**
 * Class ActiveMQ
 */
class ActiveMQ {

    /**
     * @var string
     */
    private $schema = 'tcp';

    /**
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * @var int
     */
    private $port = 61616;

    /**
     * @var string
     */
    private $user = 'admin';

    /**
     * @var string
     */
    private $password = 'admin';

    /**
     * @var int
     */
    private $connectRetryCount = 3;

    /**
     * @var int
     */
    private $retryNumber = 0;

    /**
     * @var null
     */
    private $stomp = null;

    /**
     * @var bool
     */
    private $persistent = null;

    /**
     * @var null
     */
    private $subscribedDestination = null;

    /**
     * @return ActiveMQ
     */
    public function __construct() {}

    /**
     * @return string
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * @param $schema
     * @return $this
     */
    public function schema($schema) {
        $this->schema = $schema;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @param $host
     * @return $this
     */
    public function host($host) {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @param $port
     * @return $this
     */
    public function port($port) {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param $user
     * @return $this
     */
    public function user($user) {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param $password
     * @return $this
     */
    public function password($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int
     */
    public function getConnectRetryCount() {
        return $this->connectRetryCount;
    }

    /**
     * @param $connectRetryCount
     * @return $this
     */
    public function connectRetryCount($connectRetryCount) {
        $this->connectRetryCount = $connectRetryCount;
        return $this;
    }

    /**
     * @return mixed
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
     * @throws Exception
     */
    public function createMessage() {
        try {
            $activeMqMessage = new ActiveMqMessage($this->stomp);
            if($this->persistent !== null) {
                $activeMqMessage->persistent($this->persistent);
            }

            return $activeMqMessage;
        }catch(Exception $e) {
            throw $e;
        }
	}

    /**
     * @param $destination
     * @param array $header
     * @throws Exception
     */
    public function subscribe($destination, $header = array()) {
        try {
            if($this->subscribedDestination != $destination) {
                $this->stomp->subscribe($destination, $header);
                $this->subscribedDestination = $destination;
            }
            return $this;
        }catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $destination
     * @param array $header
     * @throws Exception
     */
    public function unsubscribe($destination, $header = array()) {
        try {
            $this->stomp->unsubscribe($destination, $header);
            return $this;
        }catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $frame
     * @throws Exception
     */
    public function ack($frame) {
        try {
            $this->stomp->ack($frame);
            return $this;
        }catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @param null $destination
     * @param bool $ack
     * @return \StompFrame
     * @throws Exception
     */
    public function getMessage($destination = null, $ack = true) {
        if($this->subscribedDestination === null && $destination === null) {
            throw new Exception("Error: No destination registered to listen !, please subscribe a destination first");
        }
        try {
            if($destination != null && $this->subscribedDestination != $destination) {
                $this->subscribe($destination);
                $this->subscribedDestination = $destination;
            }

            $frame = $this->stomp->readFrame();
            if($ack === true && !empty($frame)) {
                $this->stomp->ack($frame);
            }

            return $frame;
        }catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function connect() {
		try {
            $connectionUrl = $this->schema . '://' . $this->host . ':' . $this->port;
            $this->stomp = new \Stomp($connectionUrl, $this->user, $this->password);

			return $this;
		}catch(Exception $e) {
            if($this->retryNumber < $this->connectRetryCount) {
                $this->retryNumber++;
                $this->connect();
            }else {
                throw $e;
            }
		}
	}

}