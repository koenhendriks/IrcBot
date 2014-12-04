<?php
/**
 * Urbandictionary.class.php.
 * User: koen
 * Date: 4-12-14
 * Time: 11:38
 */

class UrbanDictionary {

    /**
     * Get a definition from urban dictionary.
     *
     * @param $define
     * @return bool|string
     */
    public function define($define){
        $json = file_get_contents('http://api.urbandictionary.com/v0/define?term='.urlencode($define));
        $response = json_decode($json,1);
        if(count($response) > 0) {
            if(count($response['list']) > 0) {
                $definition = $response['list'][0];
                $explain = $response['list'][0]['definition'];
                $explain = preg_replace('/\s+\s+/', '', $explain);

                if(strlen($explain) > 340) {
                    $explain = substr($explain, 0, 300) . '...';
                    return $explain . '... Read more: (' . $response['list'][0]['permalink'] . ')';
                }else {
                    return $response['list'][0]['definition'] . '(' . $response['list'][0]['permalink'] . ')';
                }
            }
            return false;
        }
        else
            return false;
    }


} 