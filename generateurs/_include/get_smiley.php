<?php

require_once "../_include/errors.php";

$smileys1=[":'(" => "ohill.gif",
           ":(" => "frown.gif",
           ":)" => "smile.gif",
           ":/" => "ohwell.gif",
           ":??:" => "confused.gif",
           ":d" => "biggrin.gif",
           ":o" => "redface.gif",
           ":p" => "tongue.gif",
           ";)" => "wink.gif",];

$smileys2=[":24:" => "24.gif",
           ":ange:" => "ange.gif",
           ":benetton:" => "benetton.gif",
           ":bic:" => "bic.gif",
           ":bounce:" => "bounce.gif",
           ":bug:" => "bug.gif",
           ":calimero:" => "calimero.gif",
           ":crazy:" => "crazy.gif",
           ":cry:" => "cry.gif",
           ":dtc:" => "dtc.gif",
           ":eek:" => "eek.gif",
           ":eek2:" => "eek2.gif",
           ":evil:" => "evil.gif",
           ":fou:" => "fou.gif",
           ":foudtag:" => "foudtag.gif",
           ":fouyaya:" => "fouyaya.gif",
           ":fuck:" => "fuck.gif",
           ":gratgrat:" => "gratgrat.gif",
           ":hap:" => "hap.gif",
           ":gun:" => "gun.gif",
           ":hebe:" => "hebe.gif",
           ":heink:" => "heink.gif",
           ":hello:" => "hello.gif",
           ":hot:" => "hot.gif",
           ":int:" => "int.gif",
           ":jap:" => "jap.gif",
           ":kaola:" => "kaola.gif",
           ":lol:" => "lol.gif",
           ":love:" => "love.gif",
           ":mad:" => "mad.gif",
           ":miam:" => "miam.gif",
           ":mmmfff:" => "mmmfff.gif",
           ":mouais:" => "mouais.gif",
           ":na:" => "na.gif",
           ":non:" => "non.gif",
           ":ouch:" => "ouch.gif",
           ":ouimaitre:" => "ouimaitre.gif",
           ":pfff:" => "pfff.gif",
           ":pouah:" => "pouah.gif",
           ":pt1cable:" => "pt1cable.gif",
           ":sarcastic:" => "sarcastic.gif",
           ":sleep:" => "sleep.gif",
           ":sol:" => "sol.gif",
           ":spamafote:" => "spamafote.gif",
           ":spookie:" => "spookie.gif",
           ":sum:" => "sum.gif",
           ":sweat:" => "sweat.gif",
           ":vomi:" => "vomi.gif",
           ":wahoo:" => "wahoo.gif",
           ":whistle:" => "whistle.gif",];

function get_smiley($smiley="", $rang=0){

  if($smiley === ""){
    //trigger_error("get_smiley.php false on smiley === \"\"");
    return false;
  }

  $smiley=strtolower($smiley);
  $rang=(int)$rang;
  $rang=($rang >= 1 && $rang <= 10) ? $rang : 0;

  global $smileys1, $smileys2;

  if(isset($smileys1[$smiley])){
    $url="https://forum-images.hardware.fr/icones/".$smileys1[$smiley];
  }elseif(isset($smileys2[$smiley])){
    $url="https://forum-images.hardware.fr/icones/smilies/".$smileys2[$smiley];
  }else{
    $url="https://forum-images.hardware.fr/images/perso/";
    if($rang !== 0){
      $url.=$rang."/";
    }
    $url.=$smiley.".gif";
    $url=str_replace(" ", "%20", $url);
  }

  $img=@imagecreatefromgif($url);
  if($img === false){
    $img=@imagecreatefrompng($url);
  }
  if($img === false){
    $img=@imagecreatefromjpeg($url);
  }
  if($img === false){
    $img=@imagecreatefrombmp($url);
  }
  if($img === false){
    //trigger_error("get_smiley.php false on imagecreatefrom ".$smiley." ".$rang." ".$url);
  }

  return $img;

}

