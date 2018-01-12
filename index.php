<?php
// include dependent codes
include_once './engine.php';
include_once './search_result.php';
include_once './page_content_search.php';

//List of pivot links
$list_of_search_pivots = [ 
                "https://www.killerphp.com/articles/",  // a random php blog I found online 
                "http://adambien.blog/roller/abien/"    // a random Java blog i found online 
];
//check is search key word is present in request
if (isset($_GET['search'])) {
    $search_key = $_GET['search']; //Get search key from request
    $search_results = "";
    $search_engine = new Engine(); //included from  'search_engine/engine.php';

    // Search using the $search_key
    foreach ($list_of_search_pivots as $pivots) {
        //for each pivot  $set_Num_of_sub_pivots = 200;
        $search_engine->setNum_of_sub_pivots(1);
        $search_engine->searchLink($search_key, $pivots);
    }

    $search_results = $search_engine->getSearch_result();
} else {
    $search_results = "";
}
?>
<!-- Search interface -->
<html>
    <head>
        <title>My Search Engine</title>
        <style>
            .mysearch_page{
                text-align: left;
                padding-top: 1%;
                margin-left: 8%;
                margin-right: 8%
            }
            .mysearch_page2{
                text-align: center;
                padding-top: 5%
            }
            .mysearch_page3{
               font-style: italic
            }
            .mysearch_page4{
              font-weight: bolder;
              font-size: 80px;
              color: skyblue
            }
            .mysearch_page5{
              color: red
            }
            .mysearch_page6{
                height: 50px;
                width: 800px;
                font-size: 20px
            }
        </style>
    </head>
    <body>
        <div class="mysearch_page2">
            <div class="mysearch_page4">My Search Engine</div>
            <form method="GET" action="./index.php">
                <input name="search"  class="mysearch_page6"value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : "" ?>"type="text" placeholder="Search ..." />
            </form>

            <?php if (isset($_GET['search'])) { ?>
            <div class="mysearch_page">
                    <?php if (count($search_results) > 0) { ?>
                        <?php foreach ($search_results as $result) { ?>
                            <a href="<?php echo $result->getLink() ?>"><h2><u><?php echo $result->getTitle() ?></u></h2></a> 
                            <p><?php echo $result->getContent() ?></p> 
                            <small class="mysearch_page3">Search phase appears <b>[<?php echo $result->getSearch_key_count() ?>]</b> times</small> 
                            <a href="<?php echo $result->getLink() ?>"><?php echo substr($result->getLink(), 0, 50) . "..." ?></a>
                            <hr/>
                        <?php } ?>
                    <?php } else { ?>
                        <div >No Results found! Please change your search words and try again</div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </body>
</html>

