<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";
require_once "../_include/gif_encoder.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$l=isset($_GET["l"]) && (int)$_GET["l"] >=0 && (int)$_GET["l"] <= 129 ?
  (int)$_GET["l"] : 0; // limite de séparation

// smiley
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false");
  die();
}
$ws=imagesx($smiley); // largeur du smiley
$hs=imagesy($smiley); // hauteur du smiley
$l=$l === 0 || $l >= $hs ? floor($hs / 2) : $hs - $l; // limite de séparation

// frames
$imgs=[];
$dlys=[];
function add_frame($d1, $d2){
  global $imgs, $dlys, $smiley, $ws, $hs, $h, $l;
  $im=imagecreatetruecolor($ws, $h);
  if($im === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor frame $d1 $d2");
    die();
  }
  $fond=imagecolorallocate($im, 255, 255, 255);
  if($fond === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate frame $d1 $d2");
    die();
  }
  $r=imagefill($im, 0, 0, $fond);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefill frame $d1 $d2");
    die();
  }
  $r=imagecopyresampled($im, $smiley, 0, $d1, 0, 0, $ws, $l, $ws, $l);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled frame 1 $d1 $d2");
    die();
  }
  $r=imagecopyresampled($im, $smiley, 0, $d2, 0, $l, $ws, $hs - $l, $ws, $hs - $l);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled frame 2 $d1 $d2");
    die();
  }
  ob_start();
  $r=imagegif($im);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagegif frame $d1 $d2");
    die();
  }
  $imgs[]=ob_get_clean();
  $dlys[]=2;
}
$d=5; // demi ouverture
$h=$hs + ($d * 2); // hauteur du gif
// frames ouverture
for($i=0; $i <= $d; ++$i){
  add_frame($d - $i, $l + $d + $i);
}
// frames fermeture
for($i=$d; $i >= 0; --$i){
  add_frame($d - $i, $l + $d + $i);
}

$gif=new GIFEncoder($imgs, $dlys, 0, 2, 252, 254, 252, 0, "bin");

// sortie
header("Content-type:image/gif");

echo $gif->GetAnimation();

