<!DOCTYPE html>
<?php

require_once("vendor/autoload.php"); 

$feed_cache = "./feed-cache.txt";
$bodyHtml = file_get_contents($feed_cache);

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