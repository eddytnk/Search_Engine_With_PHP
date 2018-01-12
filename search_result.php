<?php

/**
 * This class gives layout information about a searched page content
 *
 * @author Edward T. Tanko
 */

class searchResult{
   
    private $position = 0;
    private $title;
    private $content = "";
    private $link = "";
     //key word occurance count
    private $search_key_count = 0;
    
    
    function getPosition() {
        return $this->position;
    }

    function getTitle() {
        return $this->title;
    }

    function getContent() {
        return $this->content;
    }

    function getLink() {
        return $this->link;
    }

    function getSearch_key_count() {
        return $this->search_key_count;
    }

    function setPosition($position) {
        $this->position = $position;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setContent($content) {
        $this->content = $content;
    }

    function setLink($link) {
        $this->link = $link;
    }

    function setSearch_key_count($search_key_count) {
        $this->search_key_count = $search_key_count;
    }

        
    /*
     * Used by array_unique() to remove dublicate object
     */
    public function __toString() {
       // return $this->title.$this->link;
        return $this->title.$this->content;
    }


}

?>