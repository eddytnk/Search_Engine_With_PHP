<?php

/**
 * This class is the engine of all searches performed
 *
 * @author Edward T. Tanko
 */

class Engine{
    
    private $search_result = array();
    
    private $cnt = 0;
    /**
     * Get all link in the pivoted page and call the search method for each link
     * @param type $search_key
     * @param type $pivot
     */
    public function searchLink($search_key,$pivot){
        //get all link in the pivot page
        // call search() function for each link
        
        $data=file_get_contents($pivot);
        
        $start = stripos($data, "<body");
        $end = stripos($data, "</body");
        //get html body
        $body = substr($data,$start,$end-$start);
        //remove all tag except <a> 
        $result_body = strip_tags($body,"<a>");
   
        $matches = array();
        //regular expression that matches most url 
    
        //$pattern = '#((https?)*://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i';
        $pattern = '#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si';

        //perform global regular expression match, ie search the entire web page for a particular thing, and store it in the previously initialised array.
        preg_match_all($pattern, $result_body, $matches);

        //add pivot to searched url list
        array_push($matches, $pivot);
        
        //remove duplicate addresses, but maintain incremental key count.
        $urlList = array_unique($matches[0]);

        //print_r($urlList);
         $related_link = array();
         $i = 0;
        foreach($urlList as $url){
            $adr = rtrim($url,'"\''); //strip " and ' at the ends
            $adr = rtrim($url,'/'); //strip " and ' at the ends
            
            //get pivot domain name from pivot URL
            $parse = parse_url($pivot);
            $pos = stripos($adr,$parse['host']); // check if pivot domain is in URL;
            if ($pos !== false) {
                $related_link[$i++] =  $adr;
            }else if(substr($adr, 0,1)=="/" && substr($adr, 1,1)!="/"){
                $related_link[$i++] =  $pivot.$adr;
            }
        }
       // print_r($related_link);
        //die();
        
        foreach($related_link as $url){
           $this->search($search_key, $url);
            //echo $search_key." ".$url."<br/>";
        }
      
    }
    /**
     * search each link and add to the search result if it content contains search key
     * 
     * @param type $search_key
     * @param type $link
     * @return type
     */
    public function search($search_key,$link){
      
       
        $ch = curl_init();
        $options = array(
            CURLOPT_URL            => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_SSL_VERIFYPEER      => False,
        );
        curl_setopt_array( $ch, $options );
        $data = curl_exec($ch); 
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($status !=200){
            return NULL;
        }

        $start = stripos($data, "<body");
        $end = stripos($data, "</body");
        //get html body
        $body = substr($data,$start,$end-$start);
       
        $result_body = strip_tags($body,"<h1><h2><h3>");
        
        $start = stripos($data, "<head");
        $end = stripos($data, "</head");
        $head = substr($data,$start,$end-$start);
        $result_head = strip_tags($head,"<title>");
      
        //Get Title of Page, if not exit get it from <h1,2,3,4 ..> tages
        $match = array("","");
       // preg_match("/<title *>(.*?)<\\title>/", $result_head, $match);
        preg_match('#<title[^>]*>(.*?)</title>#', $result_head, $match);
//        if(count($match)==0){
//            preg_match("/<h[^>]*>(.*?)<\\/h[^>]>/", $result_body, $match);
//        }
        
        $page_title = (count($match)>0)?$match[1]:NULL;
        $page_content_search = new PageContentSearch($search_key, $result_body,$page_title);
        if($page_title==NULL){
            return NULL;
        }
        
        $search_result = new searchResult();
        if(count($match)>0 && $page_content_search->getSearch_key_count_on_page()>0){
            $search_result->setTitle($match[1]);
            $search_result->setContent($page_content_search->getDisplayContent());
            $search_result->setLink($link);
            
            //Ranking rule: search_key on title = 95%, search_key on page content = 5%
            $on_title = $page_content_search->getSearch_key_count();
            $on_page = $page_content_search->getSearch_key_count_on_page();
            $rank = round((round((0.95 * $on_title),5) + round((0.05 * $on_page),5)),5);
            $search_result->setPosition($rank);
            $search_result->setSearch_key_count($on_page);
            
            $this->cnt++;
            $this->addSearch($search_result);
        }
        
        
        return $this->getSearch_result();
        
    }
    
    public function getSearch_result() {
        //remove dublicate search result objects
        //just in case two links leads to thesame content
        // sort search result by their $position value desc order
        $res =  array_unique($this->search_result);
        usort($res, array($this, "compare_by_position"));
        return $res;
    }

    public function addSearch($search_result){
        array_push($this->search_result,$search_result);
    }
    
 
    function compare_by_position($res1, $res2){
        $pos1 = $res1->getPosition();
        $pos2 = $res2->getPosition();
        if($res1>$res2){
            return -1;
        }elseif ($res1==$res2) {
            return 0;
        } else {
            return 1;
        }
    }

    
    
}
?>