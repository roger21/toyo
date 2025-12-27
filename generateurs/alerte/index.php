<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text=isset($_GET["t"]) ? $_GET["t"] : "";
$smiley=isset($_GET["s"]);

$ds=5; // decallage du texte pour l'ombre

// images
$im=imagecreatefrompng("./alerte.png");
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng alerte");
  die();
}
$test=imagecreatetruecolor(1, 1);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
  die();
}

// couleurs
$blanc=imagecolorallocate($im, 250, 250, 250);
if($blanc === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate blanc");
  die();
}
$noir=imagecolorallocate($im, 56, 0, 5);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate noir");
  die();
}

// normalise
$text=normalizer_normalize($text, Normalizer::NFKC);
if($text === false){
  trigger_error(__DIR__."/index.php died on normalizer_normalize $text");
  die();
}

// texte
$sizes=[55 => [62, 127],
        47 => [63, 119],
        42 => [65, 114],
        35 => [66, 110]];
foreach($sizes as $t => $d){
  $pos=imagefttext($test, $t, 0, 0, 0, $noir, "./arialbi.ttf", $text);
  if($pos === false){
    trigger_error(__DIR__."/index.php died on imagefttext test texte $t");
    die();
  }
  if($pos[4] <= 354 || $t === 35){
    list($dx, $dy)=$d;
    $r=imagefttext($im, $t, 0, $dx + $ds, $dy + $ds, $noir, "./arialbi.ttf", $text);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagefttext texte noir $t");
      die();
    }
    $r=imagefttext($im, $t, 0, $dx, $dy, $blanc, "./arialbi.ttf", $text);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagefttext texte blanc $t");
      die();
    }
    break;
  }
}

// sortie
header("Content-type: image/png");

if($smiley){

  $s=imagecreatetruecolor(70, 24);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagecopyresampled($s, $im, 0, 0, 0, 0, 70, 24, 427, 148);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled s");
    die();
  }
  $r=imagepng($s, null, 9);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagepng s");
    die();
  }

}else{

  $r=imagepng($im, null, 9);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagepng");
    die();
  }

}

