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

            switch($cmd){
                case 'about':
                    $this->writeChannel('Hello, I\'m '.$this->getRealname().'. I\'m here to help you.');
                    break;
                case 'whoami':
                    $this->writeChannel('You are '.$data->getUser());
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
     * @param $channel
     * @param $data
     */
    public function writeChannel($send){
        $data = $this->data;
        socket_write($this->socket ,"PRIVMSG ".$data->getReceiver()." :$send \r\n");
        $this->log("Sending to channel ".$data->getReceiver().": $send");
    }

    /**
     * Get a file size before downloading
     *
     * @param $url
     * @return int|string
     */
    public function getFileSize($url){
        // Assume failure.
        $result['size'] = 0;
        $result['status'] = 'unknown';
        $curl = curl_init( $url );

        // Issue a HEAD request and follow any redirects.
        curl_setopt( $curl, CURLOPT_NOBODY, true );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36');
        curl_setopt( $curl, CURLOPT_TIMEOUT_MS,3000);

        $data = curl_exec( $curl );
        curl_close( $curl );

        if( $data ) {
            $content_length = "unknown";
            $status = "unknown";

            if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
                $status = (int)$matches[1];
            }

            if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
                $content_length = (int)$matches[1];
            }

            // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
            if( $status == 200 || ($status > 300 && $status <= 308) ) {
                $result['size'] = $content_length;
            }

        


            $result['status'] = $status;
        }

        return $result;
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
        echo "\n";
        echo "/******************************************************************\\";
        echo "\n";
        echo "   ERROR: ".$error;
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

        if($data->isValidUser() && $data->getUser() != 'nodejsbot'){ //TODO need blacklist for users to ignore
            $message =  explode(" ",$data->getMessage());
            $matches  = preg_grep ($reg_exUrl,$message);

            if(count($matches) < 4)
            {
                foreach($matches as $link){
                    $link = $this->getLinkTitle($link);

                    if($link['urlfix'])
                        $link['title'] .= ' ('.$link['url'].')';

                    if($link['title'] != '')
                        $this->writeChannel($link['title']);
                }
            }else{
                $this->writeChannel('To many URL requests.');
            }
        }
    }

    /**
     * Get the title of a page by URL
     *
     * @param $url
     * @return mixed
     */
    public function getLinkTitle($url){
        $urlFix = false;

        if(substr($url, 0, 1) == ':')
            $url = substr($url, 1, strlen($url));

        if (strpos($url,'http') === false) {
            $url = 'http://'.$url;
            $urlFix = true;
        }

        $url = preg_replace('/\s+/', '', $url);

        $size = $this->getFileSize($url);
        
        if(isset($size['status']) && $size['status'] != 200 && ($size['status'] < 300 || $size['status'] > 307)) {
            return array('title' => 'Http error ' . $size['status'], 'urlfix' => false);
        }elseif(is_numeric($size['size']) && ($size['size'] == 0 || $size['size'] > 5000000)) {
            return array('title' => 'File is to big', 'urlFix' => false);
        }

        $str = file_get_contents($url);
        if(!$str){
            return array('title' => 'URL error', 'domain' => 'URL error', 'urlfix' => 'URL error', 'url' => 'Url error');
        }

        if(strlen($str)>0){
            preg_match("/\<title\>(.*)\<\/title\>/",$str,$title);
            $this->log('link: '.$title[1]);
            $parse = parse_url($url);
            return array('title' => $title[1], 'domain' => $parse['host'], 'urlfix' => $urlFix, 'url' => $url);
        }
    }

    /**
     * Joins the channels
     */
    public function joinChannels(){
        if(!$this->isInChannel()) {

            foreach ($this->getChannels() as $channel)
                $this->write('JOIN ' . $channel);

            $this->setInChannel(true);
        }
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