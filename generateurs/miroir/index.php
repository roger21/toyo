<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$left=isset($_GET["left"]);
$right=isset($_GET["right"]);
$top=isset($_GET["top"]);
$bottom=isset($_GET["bottom"]);
if(!$left && !$right && !$top && !$bottom){
  $left=true;
}

// images
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false");
  die();
}
$sflip=get_smiley($s, $r);
if($sflip === false){
  trigger_error(__DIR__."/index.php died on sflip === false");
  die();
}
if($left || $right){
  $r=imageflip($sflip, IMG_FLIP_HORIZONTAL);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imageflip IMG_FLIP_HORIZONTAL");
    die();
  }
}else{
  $r=imageflip($sflip, IMG_FLIP_VERTICAL);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imageflip IMG_FLIP_VERTICAL");
    die();
  }
}
$w=imagesx($smiley);
$h=imagesy($smiley);
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
$r=imagecopyresampled($im, $smiley, 0, 0, 0, 0, $w, $h, $w, $h);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled");
  die();
}

// miroir
$half_x=ceil($w / 2);
$half_y=ceil($h / 2);
$half_w=floor($w / 2);
$half_h=floor($h / 2);
if($left){
  $r=imagecopyresampled($im, $sflip, $half_x, 0, $half_x, 0, $half_w, $h, $half_w, $h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled left");
    die();
  }
}elseif($right){
  $r=imagecopyresampled($im, $sflip, 0, 0, 0, 0, $half_w, $h, $half_w, $h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled right");
    die();
  }
}elseif($top){
  $r=imagecopyresampled($im, $sflip, 0, $half_y, 0, $half_y, $w, $half_h, $w, $half_h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled top");
    die();
  }
}elseif($bottom){
  $r=imagecopyresampled($im, $sflip, 0, 0, 0, 0, $w, $half_h, $w, $half_h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled bottom");
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

