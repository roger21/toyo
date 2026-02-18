<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text=isset($_GET["t"]) ? $_GET["t"] : "";
$smiley=isset($_GET["s"]);

// images
$im=imagecreatefrompng("./seal.png");
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng seal");
  die();
}
$test=imagecreatetruecolor(1, 1);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
  die();
}

// couleur
$c=imagecolorallocate($im, 146, 134, 32);
if($c === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate c");
  die();
}

// normalise
$text=normalizer_normalize($text, Normalizer::NFKC);
if($text === false){
  trigger_error(__DIR__."/index.php died on normalizer_normalize $text");
  die();
}

// texte
$sizes=[20 => 0,
        19 => -1,
        18 => -1,
        17 => -2,
        16 => -2,
        15 => -2,
        14 => -2,
        13 => -3,
        12 => -3,
        11 => -4,
        10 => -4,
        9 => -4,
        8 => -5];
$ok=false;
foreach($sizes as $t => $cy){
  $pos=imagefttext($test, $t, 0, 0, 0, $c, "./arialb.ttf", $text);
  if($pos === false){
    trigger_error(__DIR__."/index.php died on imagefttext test texte $t");
    die();
  }
  if($pos[4] <= 104){
    $cx=floor((104 - $pos[4]) / 2);
    $r=imagefttext($im, $t, 0, 48 + $cx, 100 + $cy, $c, "./arialb.ttf", $text);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagefttext texte $t");
      die();
    }
    $ok=true;
    break;
  }
}
if($ok === false){
  $r=imagefttext($im, 20, 0, 48, 100, $c, "./arialb.ttf", "OH SHI-");
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext OH SHI-");
    die();
  }
}

// sortie
header("Content-type: image/png");

if($smiley){

  $s=imagecreatetruecolor(50, 50);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagealphablending($s, false);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagealphablending s");
    die();
  }
  $r=imagesavealpha($s, true);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagesavealpha s");
    die();
  }
  $r=imagecopyresampled($s, $im, 0, 0, 0, 0, 50, 50, 200, 200);
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

