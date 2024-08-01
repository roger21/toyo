#!/usr/bin/php
<?php


require_once("include.php");


$DARK_BACKGROUND=true;


$message="Changement dans les smileys persos entre ";


// is this a test ?

$this_is_a_test_sth=false;

if(isset($_SERVER["THIS_IS_A_TEST_STH"]) && $_SERVER["THIS_IS_A_TEST_STH"] === "TRUE"){

  $this_is_a_test_sth=true;

  trigger_error("this is a test", E_USER_NOTICE);

}else{

  trigger_error("this is NOT a test", E_USER_NOTICE);

}


// les cookies

if($this_is_a_test_sth){

  if(isset($_SERVER["HFR_COOKIES_TEST"]) && $_SERVER["HFR_COOKIES_TEST"] !== ""){

    $cookies_test=$_SERVER["HFR_COOKIES_TEST"];

    trigger_error("cookies test ok ".strlen($cookies_test), E_USER_NOTICE); // 71

  }else{

    trigger_error("NO TEST COOKIES", E_USER_ERROR);

  }

}else{

  if(isset($_SERVER["HFR_COOKIES"]) && $_SERVER["HFR_COOKIES"] !== ""){

    $cookies=$_SERVER["HFR_COOKIES"];

    trigger_error("cookies ok ".strlen($cookies), E_USER_NOTICE); // 58

  }else{

    trigger_error("NO COOKIES", E_USER_ERROR);

  }

}


// get_all

get_all();

trigger_error("get_all ok", E_USER_NOTICE);


// les dates

$command="git log -1 --format=%aI -- ../generateurs/_api/smileys.txt";

$date=exec_command($command);

trigger_error("date\n".print_r($date, true), E_USER_NOTICE);

$last_date=french_date_from_iso($date[0]);

trigger_error("last_date\n$last_date", E_USER_NOTICE);

$now_date=strtr(date_format(date_create_immutable(), "l j F Y à H:i:s"),
                $date_translation);

trigger_error("now_date\n$now_date", E_USER_NOTICE);

$message.="le $last_date et le $now_date\n\n";

trigger_error("message\n$message", E_USER_NOTICE);


// les changements dans les smileys

$command="git diff -U0 --no-color -- ../generateurs/_api/smileys.txt".
        " | grep -v \"^@@\" | tail -n +5";

$smileys=exec_command($command);

trigger_error("smileys\n".print_r($smileys, true), E_USER_NOTICE);


// changements ou pas ?

$changes=(count($smileys) !== 0);

trigger_error("changes\n".var_export($changes, true), E_USER_NOTICE);


// construction du message

if($changes){

  $sup=[];
  $add=[];
  foreach($smileys as $smiley){
    $type=substr($smiley, 0, 1);
    $smiley="[:".substr($smiley, 1)."]";
    if($type === "-"){
      $sup[$smiley]=$detail_url.rawurlencode($smiley);
    }else{
      $add[$smiley]=$detail_url.rawurlencode($smiley);
    }
  }
  $max_line=5;
  $nbsup=count($sup);
  $nbadd=count($add);
  if($nbsup > 0){
    if($nbsup > 1){
      $message.="$nbsup smileys supprimés :\n\n";
    }else{
      $message.="1 smiley supprimé :\n\n";
    }
    $cpt=0;
    foreach($sup as $s => $u){
      $message.="$s        ";
      if((++$cpt % $max_line) === 0){
        $message=trim($message)."\n\n";
      }
    }
    $message=trim($message)."\n\n";
  }
  if($nbsup > 0 && $nbadd > 0){
    $message.="\n";
  }
  if($nbadd > 0){
    if($nbadd > 1){
      $message.="$nbadd nouveaux smileys :\n\n";
    }else{
      $message.="1 nouveau smiley :\n\n";
    }
    $cpt=0;
    foreach($add as $s => $u){
      $message.="$s [url=$u]Détail[/url]        ";
      if((++$cpt % $max_line) === 0){
        $message=trim($message)."\n\n";
      }
    }
    $message=trim($message)."\n\n";
  }

}else{

  $message.="Aucun changement [:osweat]\n";

}

trigger_error("message\n$message", E_USER_NOTICE);


// le nombre de smileys persos total

$command="wc -l < ../generateurs/_api/smileys.txt | bc";

$nbsmileys=(int)exec_command($command)[0];

trigger_error("nbsmileys\n$nbsmileys", E_USER_NOTICE);

$message.=number_format($nbsmileys, 0, ",", "\u{2009}")." smileys persos au total";

trigger_error("message\n$message", E_USER_NOTICE);


// postage du message

if($this_is_a_test_sth){

  $result=post_message($message, $cookies_test, $cat_test, $topic_test, $post_test);

}else{

  $result=post_message($message, $cookies, $cat, $topic);

}

trigger_error("result\n$result", E_USER_NOTICE);


