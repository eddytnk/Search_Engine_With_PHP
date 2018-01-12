<?php

/**
 * This class gives report about the content of a page search
 *
 * @author Edward T. Tanko
 */
class PageContentSearch {
    
    private $search_key_count = 0;
    private $search_key_count_on_page = 0;
    private $search_key = "";
    private $content = "";
    private $title = "";
    
    public function __construct($search_key, $content, $title) {
        $this->search_key = $search_key;
        $this->content = $content;
        $this->title = $title;
        
        //count key words on content of page
       $this->search_key_count_on_page = substr_count(strtolower($content), strtolower($search_key));
        
         //count key words on title of page
        $this->search_key_count = substr_count(strtolower($title), strtolower($search_key));
        
    }

    
    public function getSearch_key_count_on_page() {
        return $this->search_key_count_on_page;
    }
    
    public function getSearch_key_count() {
        return $this->search_key_count;
    }

    public function getSearch_key() {
        return $this->search_key;
    }
    public function getTitle() {
        return $this->title;
    }

    public function getDisplayContent() {
        $res = substr($this->content, stripos($this->content, $this->search_key ),500);
        return strip_tags(str_replace($this->search_key, "<b>".$this->search_key."</b>", $res),"<b>");
    }
    public function getContent() {
        return $this->content;
    }


}
