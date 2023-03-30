<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètre
$text=isset($_GET["t"]) ? $_GET["t"] : "";

$w=69; // largeur
$h=50; // hauteur
$c=41; // coté carré rouge
$d=33; // diametre rond blanc
$l=25; // taille police lettre
$a=45; // angle lettre
$cx=2; // correction position lettre en x
$cy=-1; // correction position lettre en y
$t=6; // taille police texte

// images
$im=imagecreatetruecolor($w, $h);
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor im");
  die();
}
$test=imagecreatetruecolor(1, 1);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
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
$rouge=imagecolorallocate($im, 203, 0, 2);
if($rouge === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate rouge");
  die();
}

// formes
$r=imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, $blanc);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle blanc");
  die();
}
$x=floor(($w - $c) / 2);
$y=0;
$r=imagefilledrectangle($im, $x, $y, $x + $c - 1, $y + $c - 1, $rouge);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle rouge");
  die();
}
$r=imagefilledellipse($im, floor($w / 2), floor($c / 2), $d, $d, $blanc);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledellipse blanc");
  die();
}

// lettre
$pos=imagefttext($test, $l, $a, 0, 0, $noir, "./imagine.ttf", substr($text, 0, 1));
if($pos === false){
  trigger_error(__DIR__."/index.php died on imagefttext test lettre");
  die();
}
$x=floor(($w - $pos[4]) / 2) + $cx;
$y=floor(($c - $pos[5]) / 2) + $cy;
$r=imagefttext($im, $l, $a, $x, $y, $noir, "./imagine.ttf", substr($text, 0, 1));
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext lettre");
  die();
}

// texte
$pos=imagefttext($test, $t, 0, 0, 0, $noir, "./arial.ttf", $text." NAZI");
if($pos === false){
  trigger_error(__DIR__."/index.php died on imagefttext test texte");
  die();
}
$x=floor(($w - $pos[4]) / 2);
$y=$h - 1;
$r=imagefttext($im, $t, 0, $x, $y, $noir, "./arial.ttf", $text." NAZI");
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext texte");
  die();
}

// sortie
header("Content-type: image/png");

$r=imagepng($im, null, 9);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagepng");
  die();
}

