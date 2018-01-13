# Search_Engine_With_PHP
Search engine using php. It uses pivoted url as based searching points

## The application:

    * User enter a search key word
    * The application search for key phrase from remote domains
    * The application present searched results in a user friendly manner 🙂

## Assumptions:

    * In this application, we will be searching from the following list of domains (website addresses). 
      Make sure there are in their absolution form. e,g http://….
      this addresses could be coming from a file or database and we will refer to them as pivot url

    * For each search page in the pivot url the following rules will be used to rank the results
       Ranking rule: search_key appears on <title> tag gives the result 95% advantage and  
        search_key on page content gives the result 5%

## Screenshot
    ![My Search Engine](https://github.com/eddytnk/Search_Engine_With_PHP/blob/master/Screenshot-2018-1-12-MySearchEngine.png)

