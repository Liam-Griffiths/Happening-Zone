<!DOCTYPE html>

<?php

require_once("vendor/autoload.php"); 

$bodyHtml = "<h1>Loading...</h1>";
$keywords = "";

function getContent() {
    //Thanks to https://davidwalsh.name/php-cache-function for cache idea
    $file = "./feed-cache.txt";
    $current_time = time();
    $expire_time = 5 * 60;
    $file_time = filemtime($file);
    if(file_exists($file) && ($current_time - $expire_time < $file_time)) {
        $fileheadlines = "./feed-headlines.txt";
        if(file_exists($fileheadlines)) {
            echo'<script>var headlines = "';
            echo file_get_contents($fileheadlines);
            echo '";</script>';
        }

        return file_get_contents($file);
    }
    else {
        echo'<script>var headlines = "';
        $content = getFreshContent();
        echo '";</script>';

        file_put_contents($file, $content);
        return $content;
    }
}
function getFreshContent() {
    $html = "";
    $fileheadlines = "./feed-headlines.txt";     
    file_put_contents($fileheadlines, "");
    // get heartbeat array of live news feeds
    // run ai headline generator - $https://github.com/udibr/headlines
    $breakingSource = array(
        array(
            "title" => "BBC Breaking News",
            "url" => "https://twitrss.me/twitter_user_to_rss/?user=bbcbreaking"
        ),
        array(
            "title" => "Breaking 911",
            "url" => "https://twitrss.me/twitter_user_to_rss/?user=Breaking911"
        ),
        array(
            "title" => "Reuters UK Top",
            "url" => "http://feeds.reuters.com/reuters/UKTopNews"
        ),
        array(
            "title" => "Reuters Breaking",
            "url" => "https://twitrss.me/twitter_user_to_rss/?user=reuters"
        ),
        array(
            "title" => "BreakingNews",
            "url" => "https://twitrss.me/twitter_user_to_rss/?user=BreakingNews"
        ),
        array(
            "title" => "cnnbrk",
            "url" => "https://twitrss.me/twitter_user_to_rss/?user=cnnbrk"
        ),
        array(
            "title" => "SkyNewsBreak",
            "url" => "https://twitrss.me/twitter_user_to_rss/?user=SkyNewsBreak"
        ),
        array(
            "title" => "SkyNewsBreak",
            "url" => "https://twitrss.me/twitter_user_to_rss/?user=SkyNewsBreak"
        ),
        array(
            "title" => "Chan",
            "url" => "http://boards.4chan.org/pol/index.rss"
        ),
        array(
            "title" => "Chan",
            "url" => "http://boards.4channel.org/news/index.rss"
        ),
        array(
            "title" => "Chan",
            "url" => "http://boards.4channel.org/v/index.rss"
        )

        
    );

    $newsSource = array(
        array(
            "title" => "Guardian",
            "url" => "https://theguardian.com/uk/rss"
        ),
        array(
            "title" => "Daily Mail",
            "url" => "https://dailymail.co.uk/home/index.rss"
        ),
        array(
            "title" => "Telegraph",
            "url" => "https://telegraph.co.uk/rss"
        ),
        array(
            "title" => "Mirror",
            "url" => "https://mirror.co.uk/rss.xml"
        ),
        array(
            "title" => "Metro",
            "url" => "https://metro.co.uk/feed/"
        ),
        array(
            "title" => "Sun",
            "url" => "https://thesun.co.uk/feed"
        ),
        array(
            "title" => "BBC",
            "url" => "http://feeds.bbci.co.uk/news/world/rss.xml"
        ),
        array(
            "title" => "CNN",
            "url" => "http://rss.cnn.com/rss/cnn_latest.rss"
        ),
        array(
            "title" => "Fox News",
            "url" => "http://feeds.foxnews.com/foxnews/latest"
        ),
        array(
            "title" => "Drudge",
            "url" => "http://feedpress.me/drudgereportfeed"
        ),
        array(
            "title" => "Breitbart",
            "url" => "http://feeds.feedburner.com/breitbart?format=xml"
        ),
        array(
            "title" => "Daily Wire",
            "url" => "https://www.dailywire.com/rss.xml"
        )
    );


    function getFeed($url){
        $html = "";
        $rss = simplexml_load_file($url);
        $count = 0;
        $html .= '<ul>';
        
        foreach($rss->channel->item as$item) {
            $count++;
            if($count > 7){
                break;
            }

            $regex = '/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
            $item->title = preg_replace($regex, '', $item->title);

            $item->title = preg_replace('/#([\w-]+)/i', '', $item->title); // @someone
            $item->title = preg_replace('/@([\w-]+)/i', '', $item->title); // #tag
            $item->title = preg_replace('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', '', $item->title);

            if(strlen($item->title) < 6){
                break;
            }

            $html .= '<li><a href="'.htmlspecialchars($item->link).'">'.htmlspecialchars($item->title).'</a></li>';
            //$item->title = removeCommonWords($item->title);
            $item->title = mb_convert_encoding($item->title, 'UTF-8', 'UTF-8');
            $item->title = htmlentities($item->title, ENT_QUOTES, 'UTF-8');
            $item->title = addslashes($item->title);
            $item->title = trim(preg_replace('/\s\s+/', ' ',  $item->title));
            $item->title = preg_replace("/\r\n|\r|\n/", ' ', $item->title);
            $out = ucfirst(strtolower($item->title . ". "));   
            echo $out;
            $fileheadlines = "./feed-headlines.txt"; 
            file_put_contents($fileheadlines, $out, FILE_APPEND);
        }


        $html .= '</ul>';
        return $html;
    } 

    
    function escapeJavaScriptText($string)
    {
        return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
    }
        
    $html .= '<div class="col-md-6"><br><strong><h1 class="display-5" style="text-align:center; width:100%; text-decoration: underline;">Articles</h1></strong><br>';
    foreach($newsSource as $source) {
        $html .= '<h2>'.$source["title"].'</h2>';
        $html .= getFeed($source["url"]);
    }
    $html .= '</div>';
    $html .= '<div class="col-md-6"><br><strong><h1 class="display-5" style="text-align:center; width:100%; text-decoration: underline;">Breaking</h1></strong><br>';
    foreach($breakingSource as $source) {
        $html .= '<h2>'.$source["title"].'</h2>';
        $html .= getFeed($source["url"]);
    }

    $html .= '</div>';

    return $html;
}
$bodyHtml = getContent();

?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/nlp_compromise/6.5.3/nlp_compromise.min.js"></script>
    
        <style>
            body { font-size: 140%; }
            a:visited { color: #a3bcd1; }

                .sup {                
                    text-align: center !important; 
                    font-family: Helvetica, Arial, sans-serif !important; 
                    font-weight: bold !important;
                    font-style : italic !important;
                    color: #fff !important; 
                    background: black !important; 
                    padding: 10px 10px !important; 
                    text-decoration: none !important;
                    width : min-content;
                    margin : auto !important;
                }
        </style>
    </head>
    <body>
        <div class="container-fluid">
        <div class="row">
                    <div class="jumbotron jumbotron-fluid" style="margin: 0 auto; width: 100%;">
            <div class="container-fluid" style="text-align:center;">
                <strong><h1 class="display-6 sup">The Happening Post</h1></strong>
            </div>

            </div>
            </div>

            <div class="row" style="text-align:center; padding:2em;">
                <h1 class="display-4 " style="text-align:center; width:70%;  margin:0 auto; text-transform: uppercase;">

                <script>
                   
                    var nlp = window.nlp_compromise;
                    var tokens = [];
                    var doc;


                    function createTokens() {
                        var myWords={
                        'trump':'Person',
                        'brexit':'Organisation'
                        }
  
                        var nlp_text = nlp.text(headlines);

                        doc = nlp.text(headlines, myWords);
                        doc = doc.topics();
                        console.log(doc);
                        var terms = nlp_text.terms();
                        for (var i = 0; i < terms.length; i++) {
                            tokens.push(terms[i].text);
                        }
                    
                    }

                    function chooseStartingToken() {

                        var outputIndex = -1;
                        var searchNum = 0;
                        for (var n = 0; n < doc.length-1; n++) {

                            for (var w = 0; w < tokens.length-1; w++) {
                                if (tokens[w] == doc[searchNum].text) {
                                    outputIndex = w;
                                }
                            }

                            if(outputIndex != -1)
                            {
                                return tokens[outputIndex];
                            }
                            else{
                                searchNum++;
                            }
                        }
                    }

                    function findNextWord(currentWord) {

                        var nextWords = [];
                        for (var w = 0; w < tokens.length-1; w++) {
                        if (tokens[w] == currentWord) {
                            nextWords.push(tokens[w+1]);
                        }
                        }

                        var word = nextWords[Math.floor(Math.random() * nextWords.length)]; // choose a random next word
                        return word;

                    }

                    
                    function start() {
                        
                        createTokens();
                        
                        var currentWord = chooseStartingToken();
                        var sentence = currentWord + " ";
                        while (currentWord.indexOf(".") < 0) { // while we haven't found a period
                            currentWord = findNextWord(currentWord);
                            sentence += currentWord + " ";
                        }
                        
                        document.write(sentence);
                    
                    }

                    start();

                    setTimeout(function() {
                    location.reload();
                    }, 60000);

                </script>

                </h1>
                
            </div>
            
        <hr>
        <small>AI Generated Headline*</small>
            <div class="row">
        
                <?php
                    print $bodyHtml;
                ?>
                
            </div>
            
        </div>
    </body>
</html>