<?php

//Load Config and classes
require_once('config.php');
function __autoload($className) {
    if(file_exists('inc/'.$className).'.class.php')
        require_once 'inc/'.$className. '.class.php';
}

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
        $IRC->checkAfk();
        $IRC->functionHandler();
        $IRC->exec();
    }
}
