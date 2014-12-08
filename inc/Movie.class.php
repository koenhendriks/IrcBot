<?php
/**
 * Movie.class.php.
 * User: koen
 * Date: 8-12-14
 * Time: 10:51
 */

class Movie {

    /**
     * Gets movie information by a string
     *
     * @param $movie
     * @return string
     */
    public function getByString($movie){
        $url = "http://www.omdbapi.com/?t=".urlencode($movie)."&y=&plot=short&r=json";

        if($json = file_get_contents($url)){
            $info = json_decode($json);
            if(isset($info->Title) && $info->Title !=''){
                return $info->Title."(".$info->Year.") - Rating ".$info->imdbRating." - http://www.imdb.com/title/".$info->imdbID."/";
            }else{
                return 'Couldn\'t find this movie';
            }
        }else{
            return 'Could not connect.';
        }
    }

    /**
     * Gets movie information by imdb ID
     *
     * @param $id
     * @return string
     */
    public function getById($id){
        $id = ltrim($id, 't');
        $url = "http://www.omdbapi.com/?i=tt".rtrim($id)."&y=&plot=short&r=json";

        if($json = file_get_contents($url)){
            $info = json_decode($json);
            if(isset($info->Title) && $info->Title !=''){
                return $info->Title."(".$info->Year.") - Rating ".$info->imdbRating." - http://www.imdb.com/title/".$info->imdbID."/";
            }else{
                return 'Couldn\'t find this movie';
            }
        }else{
            return 'Could not connect.';
        }
    }

} 