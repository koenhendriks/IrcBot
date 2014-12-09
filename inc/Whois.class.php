<?php
/**
 * Whois.class.php.
 * User: koen
 * Date: 9-12-14
 * Time: 11:18
 */

class Whois {

    /**
     * Whois command for domain
     *
     * @param $domain
     * @return string
     */
    public function getDomain($domain){
        $url = 'https://www.whoisxmlapi.com/whoisserver/WhoisService?domainName='.rtrim($domain).'&outputFormat=json';
        $json = file_get_contents($url);
        $whois = json_decode($json);
        if(isset($whois->ErrorMessage)){
            return 'Couldn\'t find domain';
        }
        $registrant = $whois->WhoisRecord->registrant;
        if($registrant->name == '')
            $registrant = $whois->WhoisRecord->registryData->registrant;

        return rtrim($domain).' is owned by '.$registrant->name.' ('.$registrant->email.')';
    }
} 