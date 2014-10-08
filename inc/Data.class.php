<?php
/**
 * Data.php
 *
 * Created by: koen
 * Date: 10/8/14
 */

class Data {
    private $user;
    private $connection;
    private $function;
    private $receiver;
    private $message;
    private $validUser;

    /**
     * Parse raw data
     *
     * @param $data
     */
    public function __construct($data){
        $eData    = explode(" ",$data);
        for($i = 0; isset($eData[$i]); $i++) {
            $eData[$i]    = trim($eData[$i]);
        }

        $this->validUser = false;
        $userString = explode("!~", $eData[0]);

        if(count($userString) > 1) {
            $this->setUser($userString[0]);
            $this->setConnection($userString[1]);
        }else {
            $this->setUser($eData[0]);
            $this->setConnection($eData[0]);
        }



        if(isset($eData[1]))
            $this->setFunction($eData[1]);
        if(isset($eData[2]))
            $this->setReceiver($eData[2]);

        if($this->getUser() && $this->getFunction() && $this->getFunction() == 'PRIVMSG' && $this->getReceiver()) {
            $message = explode($eData[0] ." ". $eData[1] ." ". $eData[2]." :", $data);
            if(isset($message[1])) {
                $this->setMessage($message[1]);
                $this->validUser = true;
            }
        }
    }

    /**
     * Check if the receiver is a channel or person
     * @return mixed
     */
    public function getReceiverType(){
        if (strpos($this->getReceiver(),'#',0) !== false) {
            return 'channel';
        }
        return 'user';
    }

    /**
     * @return mixed
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param mixed $function
     */
    public function setFunction($function)
    {
        $this->function = $function;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param mixed $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mixed $connection
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return boolean
     */
    public function isValidUser()
    {
        return $this->validUser;
    }

    /**
     * @param boolean $validUser
     */
    public function setValidUser($validUser)
    {
        $this->validUser = $validUser;
    }





}