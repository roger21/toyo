#!/usr/bin/php
<?php

require_once "../_include/errors.php";

trigger_error("get_all.php");

$wiki_url="https://forum.hardware.fr/wikismilies.php?config=hfr.inc&alpha=ALPHA&page=";

$lettres=[
  "a",
  "b",
  "c",
  "d",
  "e",
  "f",
  "g",
  "h",
  "i",
  "j",
  "k",
  "l",
  "m",
  "n",
  "o",
  "p",
  "q",
  "r",
  "s",
  "t",
  "u",
  "v",
  "w",
  "x",
  "y",
  "z",
  "|",
];

$regexp_smiley='%<input type="hidden" name="smiley[0-9]+" value="\[:(.+?)\]" />%';

$exists=[];
$smileys=[[], []];

foreach($lettres as $lettre){

  $lettre_url=str_replace("ALPHA", $lettre, $wiki_url);

  $page_number=1;

  while($page_number === 1 || count($matches[1]) > 0){

    $page_url=$lettre_url.$page_number++;

    //echo $page_url."\n";

    $page=file_get_contents($page_url);

    preg_match_all($regexp_smiley, $page, $matches);

    foreach($matches[1] as $smiley){

      //echo $smiley."\n";

      $smiley=html_entity_decode($smiley, ENT_QUOTES | ENT_SUBSTITUTE | ENT_XML1, "UTF-8");
      if(!in_array($smiley, $exists, true)){
        $exists[]=$smiley;
        $split=explode(":", $smiley, 2);
        $smileys[0][]=$split[0];
        $smileys[1][]=count($split) === 2 ? (int)$split[1] : 0;
      }

    }

  }

}

$list="";
setlocale(LC_ALL, "fr_FR");
array_multisort($smileys[0], SORT_ASC, SORT_LOCALE_STRING | SORT_FLAG_CASE,
                $smileys[1], SORT_ASC, SORT_NUMERIC);
foreach($smileys[0] as $i => $smiley){
  $list.=$smiley.($smileys[1][$i] !== 0 ? ":".$smileys[1][$i] : "")."\n";
}
file_put_contents("../_api/smileys.txt", trim($list));

