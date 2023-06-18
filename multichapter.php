<?php
//example call: https://yourwebsite/multichapter.php?book=GEN&chapter=1&verse_start=1&chapter_end=2&verse_end=5
header('Content-Type: text/html; charset=utf-8');

$baseURL = "https://4.dbt.io/api/bibles/filesets/";
$bibleVersion = 'ENGKJV';  //change this to your bible id
$versionCode = '4';
$key = '';  //add your API key here
$book = $_GET["book"];
$chapter = $_GET["chapter"];
$verse_start=$_GET["verse_start"];
$chapter_end = $_GET["chapter_end"];
$verse_end = $_GET["verse_end"];
$verseOutput = '';

function getData($finalURL){
    global $verseOutput;
    $verseNum = 0;
    $jsondata = file_get_contents($finalURL);

    $obj = json_decode($jsondata,true);

    foreach($obj['data'] as $key => $value) {
        // print_r($value);
        foreach ($value as $subkey => $subvalue){
            if ($subkey == "verse_start"){            
                $verseNum = $subvalue;
            }
            if ($subkey == "verse_text"){            
                $verseOutput .=  $verseNum . ' ' . $subvalue . "  ";  //append verse to the output result
            }
        }        
    }
}

//if no $chapter_end, it is a "normal", single chapter URL
if (empty($_GET["chapter_end"])){
    echo 'single verse';
    $finalURL = $baseURL . $bibleVersion . '/' . $book . '/' . $chapter . '?';
    // echo $finalURL;
     //has verse, append     
    $_GET["verse_start"] ? $finalURL .= '&verse_start=' . strval($_GET["verse_start"]) : null ;    
    $_GET["verse_end"] ? $finalURL .= '&verse_end=' . strval($_GET["verse_end"]) : null ;    
    $finalURL .= '&v=' . strval($versionCode) . '&key=' . $key;
    // echo($finalURL. "<br>");
    getData($finalURL);
} else {  //multiple chapter detected, need to loop
    // echo 'multiverse';
    for ($x = intval($_GET["chapter"]); $x <= intval($_GET["chapter_end"]); $x++) {
        $finalURL = $baseURL . $bibleVersion . '/' . $book . '/' . $x . '?';
        $x ==($_GET["chapter"]) && $_GET["verse_start"] ? $finalURL .= '&verse_start=' . strval($_GET["verse_start"]) : null ;    
        $x ==($_GET["chapter_end"]) && $_GET["chapter_end"] ? $finalURL .= '&verse_end=' . strval($_GET["verse_end"]) : null ; 
        $finalURL .= '&v=' . strval($versionCode) . '&key=' . $key;
        // echo($finalURL. "<br>");
        getData($finalURL);
        $verseOutput .= '<br><br>';
    }
}

echo($verseOutput);
?>
