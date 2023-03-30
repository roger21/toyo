<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text=isset($_GET["t"]) ? $_GET["t"] : "";
$smiley=isset($_GET["s"]);

// images
$im=imagecreatefrompng("./ump.png");
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng ump");
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

// texte
foreach([18, 14, 11] as $t){
  $pos=imagefttext($test, $t, 0, 0, 0, $blanc, "./arialb.ttf", $text);
  if($pos === false){
    trigger_error(__DIR__."/index.php died on imagefttext test texte $t");
    die();
  }
  if($pos[4] <= 140){
    $x=2 + floor((140 - $pos[4]) / 2);
    break;
  }
  $x=2;
}
$r=imagefttext($im, $t, 0, $x + 2, 65, $noir, "./arialb.ttf", $text);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext texte noir");
  die();
}
$r=imagefttext($im, $t, 0, $x, 63, $blanc, "./arialb.ttf", $text);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext texte blanc");
  die();
}

// sortie
header("Content-type: image/png");

if($smiley){

  $s=imagecreatetruecolor(70, 36);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagecopyresampled($s, $im, 0, 0, 0, 0, 70, 36, 143, 75);
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

