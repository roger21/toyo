#!/usr/bin/php
<?php


require_once("include.php");


//$DARK_BACKGROUND=true;


$message="Changement dans les smileys persos entre ";


// les dates

$command="git log -2 --format=%aI -- ../generateurs/_api/smileys.txt";

$dates=exec_command($command);

trigger_error("date\n".print_r($dates, true), E_USER_NOTICE);

$last_date=french_date_from_iso($dates[0]);

trigger_error("last_date\n$last_date", E_USER_NOTICE);

$past_date=french_date_from_iso($dates[1]);

trigger_error("past_date\n$past_date", E_USER_NOTICE);

$message.="le $past_date et le $last_date\n\n";

trigger_error("message\n$message", E_USER_NOTICE);


// les changements dans les smileys

$command="git log --format=\"\" -p -1 -U0 --no-color -- ../generateurs/_api/smileys.txt | grep -v \"^@@\" | tail -n +5";

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
      $message.="$s [url=$u]details[/url]        ";
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

echo "\n".$message."\n\n\n\n";


