<?php
/**
 * Xkcd.class.php.
 * User: koen
 * Date: 5-12-14
 * Time: 12:14
 */

class Xkcd {

    /**
     * Construct an xkcd item
     *
     * @param $id
     */
    public function __construct($id)
    {
        $json = $this->getJson($id);
        if(!$json)
            return false;
        $xkcd = json_decode($json);
        $this->xkcd = 'xkcd: '.html_entity_decode($xkcd->safe_title).' ('.$xkcd->img.')';
    }

    /**
     * Get the string
     *
     * @return string
     */
    public function get(){
        return $this->xkcd;
    }

    public function getJson($id){
        $json = file_get_contents("http://xkcd.com/".rtrim($id)."/info.0.json");
        return $json;
    }
} 