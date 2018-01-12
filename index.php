<?php
    
include_once './engine.php';
include_once './search_result.php';
include_once './page_content_search.php';

    $list_of_search_pivots = [
        "http://www.cnn.com/",
        "http://www.foxnews.com/",
        "http://www.aljazeera.com/",
        ];


    if(isset($_GET['search'])){
         $search_key = $_GET['search'];
         $search_results = "";
         $search_engine = new Engine();
         
         // Search using the $search_key
         foreach ($list_of_search_pivots as $pivots){
            $search_engine->searchLink($search_key, $pivots);
         }
         // Search using each word on the $search_key
         $words = explode($search_key, " ");
         if(count($words)>1){
            foreach($words as $key){
               foreach ($list_of_search_pivots as $pivots){
                 $search_engine->searchLink($key, $pivots);
               }
            }
         }
         
         $search_results = $search_engine->getSearch_result();
    }else{
         $search_results = "";
    }
   
    
?>


<html>
    <head>
        <title>My Search Engine</title>
    </head>
    <body>
        <div style="text-align: center;padding-top: 5%">
            <div style="font-weight: bolder;font-size: 80px;color: skyblue">My Search Engine</div>
            <form method="GET" action="./index.php">
                <input name="search"  value="<?php echo (isset($_GET['search']))? $_GET['search']:"" ?>"type="text" placeholder="Search ..."  style=" height: 50px;width: 800px;font-size: 20px"/>
            </form>
            
            <?php if(isset($_GET['search'])){ ?>
            <div style="text-align: left;padding-top: 1%;margin-left: 8%;margin-right: 8%">
                <?php if (count($search_results)>0){?>
                    <?php foreach ($search_results as $result){  ?>
                        <a href="<?php echo $result->getLink()?>"><h2><u><?php echo $result->getTitle()?></u></h2></a> 
                        <p><?php echo $result->getContent()?></p> 
                        <small style="font-style: italic">Search phase appears <b>[<?php echo $result->getSearch_key_count()?>]</b> times</small> 
                        <a href="<?php echo $result->getLink()?>"><?php echo substr($result->getLink(),0,50)."..."?></a>
                        <hr/>
                    <?php } ?>
                <?php }else{ ?>
                        <div style="color: red">No Results found! Please change your search words and try again</div>
                 <?php } ?>
            </div>
             <?php } ?>
        </div>
    </body>
</html>