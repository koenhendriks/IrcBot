<?php

require_once('inc/Registry.class.php');
require_once('inc/IRC.class.php');
require_once('inc/Link.class.php');
require_once('inc/Data.class.php');
require_once('inc/UrbanDictionary.class.php');
require_once('inc/Xkcd.class.php');
require_once('inc/Movie.class.php');
require_once('inc/Whois.class.php');
require_once('inc/Random.class.php');
require_once('config.php');

//Construct min IRC class with settings
$IRC = new IRC(NICKNAME, REALNAME, IDENT, NICK_PASS, $channels);

/**
 * Main socket loop on the IRC network.
 */
while($data = socket_read($IRC->socket ,65000,PHP_NORMAL_READ)) {
    if($data == "\n") continue;

    $IRC->setRawData($data);
    $IRC->joinChannels();
    $IRC->identify();
    if($IRC->isInChannel()){
        $IRC->handleURL();
        $IRC->functionHandler();
        $IRC->stayAlive();
        $IRC->exec();
    }
}
