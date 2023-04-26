<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";
require_once "../_include/gif_encoder.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$t=(isset($_GET["t"]) && (int)$_GET["t"] >= 1 && (int)$_GET["t"] <= 10) ?
  (int)$_GET["t"] : 2; // tours
$m=(isset($_GET["m"]) && (int)$_GET["m"] >= 1 && (int)$_GET["m"] <= 5) ?
  (int)$_GET["m"] : 3; // mode
$dx=(isset($_GET["dx"]) && (int)$_GET["dx"] >= -100 && (int)$_GET["dx"] <= 100) ?
   (int)$_GET["dx"] : 0; // delta x
$v=(isset($_GET["v"]) && (int)$_GET["v"] >= 1 && (int)$_GET["v"] <= 10) ?
  (int)$_GET["v"] : 6; // vitesse
$dly=52 - (($v + 15) * 2); // de 20 à 2 par pas de 2 (i.e. de 200ms à 20ms)

// smiley
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false $s $r");
  die();
}
$ws=imagesx($smiley); // largeur du smiley
$hs=imagesy($smiley); // hauteur du smiley

// smiley rotaté à 45°
$fond=imagecolorat($smiley, 0, 0);
if($fond === false){
  trigger_error(__DIR__."/index.php died on imagecolorat test");
  die();
}
$test=imagerotate($smiley, 45, $fond);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagerotate test");
  die();
}
$l=imagesx($test); // côté du smiley rotaté ($test est un carré)

// frames
$imgs=[];
$dlys=[];
function add_frame($angle, $delta, $rond=true){
  global $imgs, $dlys, $smiley, $ws, $hs, $w, $max, $dly;
  $frame=imagecreatetruecolor($w, $max);
  if($frame === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor frame $angle $delta");
    die();
  }
  $fond=imagecolorallocate($frame, 255, 255, 255);
  if($fond === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate frame $angle $delta");
    die();
  }
  $r=imagefill($frame, 0, 0, $fond);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefill frame $angle $delta");
    die();
  }
  $im=imagecreatetruecolor($ws, $hs);
  if($im === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor frame im $angle $delta");
    die();
  }
  $fond=imagecolorallocate($im, 255, 255, 255);
  if($fond === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate frame im $angle $delta");
    die();
  }
  $r=imagefill($im, 0, 0, $fond);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefill frame im $angle $delta");
    die();
  }
  $r=imagecopyresampled($im, $smiley, 0, 0, 0, 0, $ws, $hs, $ws, $hs);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled frame im $angle $delta");
    die();
  }
  if($angle % 360){
    $im=imagerotate($im, $angle, $fond);
    if($im === false){
      trigger_error(__DIR__."/index.php died on imagerotate frame im $angle $delta");
      die();
    }
  }
  $wr=imagesx($im);
  $hr=imagesy($im);
  if($rond){
    $x=ceil(($max - $wr) / 2);
    $y=ceil(($max - $hr) / 2);
  }else{ // carre (claqué au sol)
    $x=0;
    $y=$max - $hr;
  }
  $r=imagecopyresampled($frame, $im, $delta + $x, $y, 0, 0, $wr, $hr, $wr, $hr);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled frame $angle $delta");
    die();
  }
  ob_start();
  $r=imagegif($frame);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagegif frame $angle $delta");
    die();
  }
  $imgs[]=ob_get_clean();
  $dlys[]=$dly;
}
$nbf=$t * 8; // nombre de frames aller / retour
$max=max($ws, $hs, $l); // hauteur du gif
switch($m){
case 1:
  $g=min($ws, $hs); // petite longueur du smiley
  break;
case 2:
  $g=max($ws, $hs); // grande longueur du smiley
  break;
case 3:
  $g=ceil(sqrt(($ws ** 2) + ($hs ** 2))); // diagonale du smiley
  break;
case 4:
  $g=ceil(sqrt(($l ** 2) * 2)); // diagonale du smiley rotaté
  break;
case 5: // carré
  $ds=[0, ceil($ws - ((M_SQRT2  * $ws) / 2)),
       $ws, ceil($ws + $hs - ((M_SQRT2  * $hs) / 2))];
  break;
}
if($m !== 5){ // rond
  $d=ceil((M_PI * $g) / 8); // delta
  $w=$max + max($nbf * ($d + $dx), 0); // largeur du gif
  // frames aller
  for($i=0; $i <= $nbf; ++$i){
    add_frame(-$i * 45, max($i * $d + $i * $dx, 0));
  }
  // frames retour
  for($i=$nbf - 1; $i > 0; --$i){
    add_frame(-$i * 45, max($i * $d + $i * $dx, 0));
  }
}else{ // carré
  $w=max(($t * ($ws + $hs) * 2) + $ws + ($nbf * $dx), $max); // largeur du gif
  // frames aller
  for($i=0; $i <= $nbf; ++$i){
    $dt=floor($i / 4) * ($ws + $hs);
    add_frame(-$i * 45, max($dt + $ds[$i % 4] + $i * $dx, 0), false);
  }
  // frames retour
  for($i=$nbf - 1; $i > 0; --$i){
    $dt=floor($i / 4) * ($ws + $hs);
    add_frame(-$i * 45, max($dt + $ds[$i % 4] + $i * $dx, 0), false);
  }
}

$gif=new GIFEncoder($imgs, $dlys, 0, 2, 252, 254, 252, 0, "bin");

// sortie
header("Content-type:image/gif");

echo $gif->GetAnimation();

