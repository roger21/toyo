<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text=isset($_GET["t"]) ? $_GET["t"] : "";
$smiley=isset($_GET["s"]);

// images
$seagal=imagecreatefrompng("./seagal.png");
if($seagal === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng seagal");
  die();
}
$im=imagecreatetruecolor(62, 73);
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor im");
  die();
}
$test=imagecreatetruecolor(1, 1);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
  die();
}
$r=imagecopyresized($im, $seagal, 1, 1, 0, 0, 60, 58, 60, 58);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresized seagal");
  die();
}

// couleurs
$blanc=imagecolorallocate($im, 255, 255, 255);
if($blanc === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate blanc");
  die();
}
$noir=imagecolorallocate($im, 0, 0, 0);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate noir");
  die();
}
$rouge=imagecolorallocate($im, 99, 18, 32);
if($rouge === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate rouge");
  die();
}

// formes
$r=imagerectangle($im, 0, 0, 61, 59, $rouge);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagerectangle rouge 1");
  die();
}
$r=imagerectangle($im, 0, 59, 61, 72, $rouge);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagerectangle rouge 2");
  die();
}
$r=imagefilledrectangle($im, 1, 60, 60, 71, $blanc);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle blanc");
  die();
}

// normalise
$text=normalizer_normalize($text, Normalizer::NFKC);
if($text === false){
  trigger_error(__DIR__."/index.php died on normalizer_normalize $text");
  die();
}

// texte
$pos=imagefttext($test, 8, 0, 0, 0, $noir, "./arial.ttf", $text);
if($pos === false){
  trigger_error(__DIR__."/index.php died on imagefttext test texte");
  die();
}
if($pos[4] <= 58){
  $x=floor((58 - $pos[4]) / 2);
  $r=imagefttext($im, 8, 0, 2 + $x, 70, $noir, "./arial.ttf", $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext texte");
    die();
  }
}else{
  $r=imagefttext($im, 7, 0, 2, 69, $noir, "./arial.ttf", $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext texte long");
    die();
  }
}

// sortie
header("Content-type: image/png");

if($smiley){

  $s=imagecreatetruecolor(42, 50);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagecopyresampled($s, $im, 0, 0, 0, 0, 42, 50, 62, 73);
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

