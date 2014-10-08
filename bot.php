<?php

require_once('inc/IRC.class.php');

//Settings
define('NICKNAME', 'Botnick');
define('REALNAME', 'Ro Bot');
define('IDENT', 'botter');
define('NICK_PASS', 'secret');
define('HOSTNAME', 0);
define('SERVER', 'irc.freenode.net');
define('PORT', 6667);
$channels = array('#channel');

//Construct min IRC class with settings
$IRC = new IRC(NICKNAME, REALNAME, IDENT, NICK_PASS, $channels);

/**
 * Main socket loop on the IRC network.
 */
while($data = socket_read($IRC->socket ,65000,PHP_NORMAL_READ)) {
    if($data == "\n") continue;

    $IRC->log($data);

    $eData    = explode(" ",$data);
    for($i = 0; isset($eData[$i]); $i++) {
        $eData[$i]    = trim($eData[$i]);
    }

    $IRC->setRawData($eData);
    $IRC->joinChannels();
    $IRC->identify();
    if($IRC->isInChannel()){
        $IRC->handleURL();
        $IRC->stayAlive();
        $IRC->exec();
    }
}
