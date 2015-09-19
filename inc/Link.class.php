<?php
/**
 * Link.class.php.
 * User: koen
 * Date: 4-12-14
 * Time: 9:59
 */

class Link {

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
            return array('title' => 'Http error ' . $size['status'], 'urlfix' => false, 'size' =>  $size['size']);
        }elseif(is_numeric($size['size']) && ($size['size'] == 0 || $size['size'] > 5000000)) {
            return array('title' => 'File is to big', 'urlFix' => false, 'size' =>  $size['size']);
        }

        $str = file_get_contents($url);
        if(!$str){
            return array('title' => 'URL error', 'domain' => 'URL error', 'urlfix' => 'URL error', 'url' => 'Url error', 'size' => 'URL Error');
        }

        if(strlen($str)>0){
            preg_match("/\<title\>(.*)\<\/title\>/",$str,$title);
            $parse = parse_url($url);

            //Make sure we actually find a title
            if(count($title) < 2)
                $title = ['Title not found','Title not found'];

            return array('title' => html_entity_decode($title[1]), 'domain' => $parse['host'], 'urlfix' => $urlFix, 'url' => $url, 'size' =>  $size['size']);
        }
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
        curl_setopt( $curl, CURLOPT_TIMEOUT_MS,10000);

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
}