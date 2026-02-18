<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text=isset($_GET["t"]) ? $_GET["t"] : ""; // texte
$taille=(isset($_GET["taille"]) && (int)$_GET["taille"] >= 0 &&
         (int)$_GET["taille"] <= 3) ? (int)$_GET["taille"] : 3; // taille
$smiley=isset($_GET["s"]); // smiley

// taille de la police
$taille=[18, 14, 11, 9][$taille];

$w=144;
$halfw=72;
$thw=140;
$h=75;
$dx=2;
$dy=63;
$sw=70;

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
$color=imagecolorallocate($test, 250, 250, 250);
if($color === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate test color");
  die();
}

// normalise
$text=normalizer_normalize($text, Normalizer::NFKC);
if($text === false){
  trigger_error(__DIR__."/index.php died on normalizer_normalize $text");
  die();
}

// taille du texte
$pos=imagefttext($test, $taille, 0, 0, 0, $color, "./arialb.ttf", $text);
if($pos === false){
  trigger_error(__DIR__."/index.php died on imagefttext test texte $taille");
  die();
}
if($pos[4] <= $thw){
  $dx=$dx + floor(($thw - $pos[4]) / 2);
}

// new image
if($pos[4] > $thw){
  $newhalfw=ceil($pos[4] / 2);
  $neww=($newhalfw * 2) + 4;
  $newim=imagecreatetruecolor($neww, $h);
  if($newim === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor newim");
    die();
  }
  $blue=imagecolorallocate($newim, 35, 62, 153);
  if($blue === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate newim blue");
    die();
  }
  $rouge=imagecolorallocate($newim, 173, 30, 55);
  if($rouge === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate newim rouge");
    die();
  }
  $r=imagefilledrectangle($newim, 0, 0, $neww - 1, $h - 1, $blue);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefilledrectangle newim bleu");
    die();
  }
  $r=imagefilledrectangle($newim, $newhalfw, 0, $neww - 1, $h - 1, $rouge);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefilledrectangle newim rouge");
    die();
  }
  $r=imagecopyresized($newim, $im, $newhalfw - $halfw, 0, 0, 0, $w, $h, $w, $h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresized im");
    die();
  }
  $im=$newim;
  $w=$neww;
}

// couleurs
$blanc=imagecolorallocate($im, 250, 250, 250);
if($blanc === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate blanc im");
  die();
}
$noir=imagecolorallocate($im, 56, 0, 5);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate noir im");
  die();
}

// texte
$r=imagefttext($im, $taille, 0, $dx + 2, $dy + 2, $noir, "./arialb.ttf", $text);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext texte noir");
  die();
}
$r=imagefttext($im, $taille, 0, $dx, $dy, $blanc, "./arialb.ttf", $text);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext texte blanc");
  die();
}

// sortie
header("Content-type: image/png");

if($smiley){
  $sh=floor($h * $sw / $w);
  $s=imagecreatetruecolor($sw, $sh);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagecopyresampled($s, $im, 0, 0, 0, 0, $sw, $sh, $w, $h);
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

