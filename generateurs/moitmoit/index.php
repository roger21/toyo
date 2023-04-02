<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s1=isset($_GET["s1"]) ? $_GET["s1"] : ""; // smiley 1
$r1=isset($_GET["r1"]) ? $_GET["r1"] : 0; // rang 1
$s2=isset($_GET["s2"]) ? $_GET["s2"] : ""; // smiley 2
$r2=isset($_GET["r2"]) ? $_GET["r2"] : 0; // rang 2
$v=isset($_GET["v"]); // vertical

// images
$smiley1=get_smiley($s1, $r1);
if($smiley1 === false){
  //trigger_error(__DIR__."/index.php died on smiley1 === false $s1 $r1");
  die();
}
$smiley2=get_smiley($s2, $r2);
if($smiley2 === false){
  //trigger_error(__DIR__."/index.php died on smiley2 === false $s2 $r2");
  die();
}
$w1=imagesx($smiley1); // largeur du smiley 1
$half_w1=ceil($w1 / 2); // demi largeur du smiley 1
$h1=imagesy($smiley1); // hauteur du smiley 1
$half_h1=ceil($h1 / 2); // demi hauteur du smiley 1
$w2=imagesx($smiley2); // largeur du smiley 2
$half_w2=floor($w2 / 2); // demi largeur du smiley 2
$half_x2=ceil($w2 / 2); // position du smiley 2 en x
$h2=imagesy($smiley2); // hauteur du smiley 2
$half_h2=floor($h2 / 2); // demi hauteur du smiley 2
$half_y2=ceil($h2 / 2); // position du smiley 2 en y
if($v){
  $w=$half_w1 + $half_w2; // largeur du moit-moit
  $h=max($h1, $h2); // hauteur du moit-moit
}else{
  $w=max($w1, $w2); // largeur du moit-moit
  $h=$half_h1 + $half_h2; // hauteur du moit-moit
}
$im=imagecreatetruecolor($w, $h);
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor");
  die();
}
$r=imagealphablending($im, false);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagealphablending");
  die();
}
$r=imagesavealpha($im, true);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagesavealpha");
  die();
}
$t=imagecolorallocatealpha($im, 0, 0, 0, 127);
if($t === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocatealpha");
  die();
}
$r=imagefill($im, 0, 0, $t);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill");
  die();
}

// moit-moit
if($v){
  $dy1=($h1 < $h) ? floor(($h - $h1) / 2) : 0;
  $r=imagecopyresampled($im, $smiley1, 0, $dy1, 0, 0, $half_w1, $h1, $half_w1, $h1);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled v smiley1");
    die();
  }
  $dy2=($h2 < $h) ? floor(($h - $h2) / 2) : 0;
  $r=imagecopyresampled($im, $smiley2, $half_w1, $dy2, $half_x2, 0,
                        $half_w2, $h2, $half_w2, $h2);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled v smiley2");
    die();
  }
}else{
  $dx1=($w1 < $w) ? floor(($w - $w1) / 2) : 0;
  $r=imagecopyresampled($im, $smiley1, $dx1, 0, 0, 0, $w1, $half_h1, $w1, $half_h1);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled smiley1");
    die();
  }
  $dx2=($w2 < $w) ? floor(($w - $w2) / 2) : 0;
  $r=imagecopyresampled($im, $smiley2, $dx2, $half_h1, 0, $half_y2,
                        $w2, $half_h2, $w2, $half_h2);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled smiley2");
    die();
  }
}

// sortie
header("Content-type: image/png");

$r=imagepng($im, null, 9);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagepng");
  die();
}

