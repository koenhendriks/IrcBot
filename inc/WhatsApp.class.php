<?php
/**
 * Whatsapp.class.php.
 * User: koen
 * Date: 26-5-15
 * Time: 9:39
 */

/**
 * Class Whatsapp
 * Get a whatsapp message when being mentioned in the channel.
 */
class WhatsApp {

    public static function sendMessage($number, $message)
    {
        $url = 'http://datspoon.nl/whapp/api/send';
        $data = array(
            "api_key" => WHAPPKEY,
            "to" => trim($number,'+'),
            "body" => $message
        );

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}