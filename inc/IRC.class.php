<?php
/**
 * IRC.php
 *
 * Created by: koen
 * Date: 10/6/14
 */

class IRC {

    private $nickname;
    private $realname;
    private $ident;
    private $nick_pass;
    private $hostname;
    private $server;
    private $port;
    private $channels;
    private $inChannel = false;
    private $rawData;
    private $currentChannel;
    public $socket;

    /**
     * @var Data
     */
    public $data;


    /**
     * Construct of the IRC bot. Socket created here.
     *
     * @param $nickname
     * @param $realname
     * @param $ident
     * @param string $nick_pass
     * @param array $channels
     * @param string $server
     * @param int $port
     * @param int $hostname
     */
    public function __construct($nickname, $realname, $ident, $nick_pass ='', $channels = array(), $server = 'irc.freenode.net', $port = 6667, $hostname = 0){

        //Set the config
        $this->setNickname($nickname);
        $this->setRealname($realname);
        $this->setIdent($ident);
        $this->setNickPass($nick_pass);
        $this->setChannels($channels);
        $this->setServer($server);
        $this->setPort($port);
        $this->setHostname($hostname);

        //Create socket
        if(!$this->socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) {
            $this->error("Couldn't create socket.");
        }
        $this->log('Socket Created');

        //Bind socket
        if(!socket_bind($this->socket ,$this->getHostname())) {
            $this->error('Couldn\'t link connection to hostname '.$this->getHostname().'.');
        }
        $this->log('Connection linked to hostname '.$this->getHostname());

        //Connect socket
        if(!socket_connect($this->socket ,$this->getServer(),$this->getPort())) {
            $this->error('Couldn\'t connect to server '.$this->getServer().' on port '.$this->getPort());
        }
        $this->log('Connecting...');

        $this->write('USER '.$this->getIdent().' '.$this->getHostname().' '.$this->getServer().' :'.$this->getRealname());
        $this->write('NICK '.$this->getNickname());


    }

    /**
     * Exec a command on irc server
     */
    public function exec(){
        $data = $this->data;

        if(substr($data->getMessage(), 0, 1) == '!'){

            $params = explode(" ", $data->getMessage());

            $cmd = ltrim ($params[0], '!');
            $cmd = preg_replace('/\s+/', '', $cmd);

            $newMessage = explode($cmd." ", $data->getMessage());

            $values = false;
            if(isset($newMessage[1])){
                $rawValues = $newMessage[1];
                $values = explode(" ", $newMessage[1]);
            }

            switch($cmd){
                case 'random':
                    $random = new Random();
                    $this->writeChannel($random->getSentence());
                    break;
                case 'about':
                    $this->writeChannel('Hello, I\'m '.$this->getRealname().'. I\'m here to help you.');
                    break;
                case 'whoami':
                    $this->writeChannel('You are '.$data->getUser());
                    break;
                case 'define':
                case 'd':
                    if(!$values)
                        $this->writeChannel($data->getUser().': define what?');
                    else{
                        if(isset($rawValues)) {
                            $Ud = new UrbanDictionary();
                            $definition = $Ud->define($rawValues);
                            if (!$definition)
                                $this->writeChannel($data->getUser() . ': I have no idea what that means.');
                            else
                                $this->writeChannel($data->getUser() . ': ' . $definition);
                        }
                    }
                    break;
                default:
                    $this->log("Unkown command: ".$cmd);
                    break;
            }
        }
    }

    /**
     * Write to a channel
     *
     * @param $send
     * @internal param $channel
     * @internal param $data
     */
    public function writeChannel($send){
        $data = $this->data;
        socket_write($this->socket ,"PRIVMSG ".$data->getReceiver()." :$send \r\n");
        $this->log("Sending to channel ".$data->getReceiver().": $send");
    }

    /**
     * Write data to IRC server.
     *
     * @param $data
     */
    public function write($data) {
        socket_write($this->socket ,$data."\r\n");
        $this->log("Sending: ".$data);
    }

    /**
     * Show error and kill script
     *
     * @param $error
     */
    public function error($error){
        if($this->isInChannel())
            $this->writeChannel('Fatal error, see log for details.');
        echo "\n";
        echo "/******************************************************************\\";
        echo "\n";
        echo "\n";
        echo "   ERROR: ".$error;
        echo "\n";
        echo "\n";
        echo "\\******************************************************************/";
        echo "\n";

        exit();
    }

    /**
     * Log to file and output
     * @param $log
     */
    public function log($log){
        echo "\n $log \n";

        $filename = 'log-'.date('dmY',time()).'.txt';

        if (!is_writable($filename))
            fopen($filename, "w");

            if (!$handle = fopen($filename, 'a'))
                $this->error("Cannot open log ($filename)");

            if (fwrite($handle, $log."\n") === FALSE)
                $this->error("Cannot write to log ($filename)");

            fclose($handle);
    }

    /**
     * Check for urls in the users message
     */
    public function handleURL(){
        // The Regular Expression filter
        $reg_exUrl = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';
        $data = $this->data;
        $link = new Link();

        if($data->isValidUser() && $data->getUser() != 'nodejsbot'){ //TODO need blacklist for users to ignore
            $message =  explode(" ",$data->getMessage());
            $matches  = preg_grep ($reg_exUrl,$message);

            if(count($matches) < 4)
            {
                foreach($matches as $url){
                    $url = $link->getLinkTitle($url);

                    if($url['urlfix'])
                        $url['title'] .= ' ('.$url['url'].')';

                    if($url['title'] != '')
                        $this->writeChannel($url['title']);
                }
            }else{
                $this->writeChannel('To many URL requests.');
            }
        }
    }

    /**
     * Handles functions that are applied on the bot
     */
    public function functionHandler()
    {
        $data = $this->data;
        switch($data->getFunction()){
            case 'JOIN':
                $this->log('User '.$data->getUser().' joined '.$data->getReceiver());
                break;
            case 'KICK':
                if($data->getMessage() == $this->getNickname()) {
                    $this->log('I was kicked from ' . $data->getReceiver() . ' Trying to rejoin now.');
                    $this->joinChannel($data->getReceiver());
                }
                break;
            default:
                $this->log('Function '.$data->getFunction().' was called, no action executed');
        }
    }

    /**
     * Joins the channels
     */
    public function joinChannels(){
        if(!$this->isInChannel()) {

            foreach ($this->getChannels() as $channel)
                $this->joinChannel($channel);

            $this->setInChannel(true);
        }
    }

    /**
     * Joins a single channel
     *
     * @param $channel
     */
    public function joinChannel($channel){
        $this->write('JOIN ' . $channel);
    }

    /**
     * Identify bot at nickserv
     */
    public function identify(){
        $data = $this->getRawData();
        if(isset($data[3]) && $data[3] == ':End')
            $this->write('PRIVMSG nickserv :identify '.$this->getIdent().' '.$this->getNickPass());
    }

    /**
     * Responds to PING from IRC server so we dont get a broken pipe or ping timeout
     */
    public function stayAlive(){
        $data = $this->data;
        if($data->getUser() == 'PING') {
            $this->write('PONG '.$data->getFunction());
        }
    }

    /**
     *
     *            Getters and Setter
     * ========================================
     */

    /**
     * @return mixed
     */
    public function getCurrentChannel()
    {
        return $this->currentChannel;
    }

    /**
     * @param mixed $currentChannel
     */
    public function setCurrentChannel($currentChannel)
    {
        $this->currentChannel = $currentChannel;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * sets raw data and creates new Data class for further use
     * @param mixed $rawData
     */
    public function setRawData($rawData)
    {
        $this->log($rawData);
        $this->rawData = $rawData;
        $this->data = new Data($rawData);

        if($this->data->getReceiverType() == 'channel' && $this->data->getFunction() == 'PRIVMSG'){
            $this->log("User: ".$this->data->getUser()." says:");
            $this->log($this->data->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param array $channels
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
    }

    /**
     * @return int
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param int $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @return string
     */
    public function getIdent()
    {
        return $this->ident;
    }

    /**
     * @param string $ident
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;
    }

    /**
     * @return string
     */
    public function getNickPass()
    {
        return $this->nick_pass;
    }

    /**
     * @param string $nick_pass
     */
    public function setNickPass($nick_pass)
    {
        $this->nick_pass = $nick_pass;
    }

    /**
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getRealname()
    {
        return $this->realname;
    }

    /**
     * @param string $realname
     */
    public function setRealname($realname)
    {
        $this->realname = $realname;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param string $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * @return boolean
     */
    public function isInChannel()
    {
        return $this->inChannel;
    }

    /**
     * @param boolean $inChannel
     */
    public function setInChannel($inChannel)
    {
        $this->inChannel = $inChannel;
    }

    /**
     * @return mixed
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @param mixed $socket
     * @return mixed
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;
        return $this->socket;
    }
}
