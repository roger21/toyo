<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$angles=["45", "90", "135", "180", "225", "270", "315"];
$a=(isset($_GET["a"]) && in_array($_GET["a"], $angles, true)) ? $_GET["a"] : "180"; // angle

// images
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false");
  die();
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
$r=imagecopyresampled($im, $smiley, 0, 0, 0, 0, $w, $h, $w, $h);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled");
  die();
}

// rotate
$rim=imagerotate($im, $a, $t);
if($rim === false){
  trigger_error(__DIR__."/index.php died on imagerotate");
  die();
}
$r=imagealphablending($rim, false);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagealphablending rim");
  die();
}
$r=imagesavealpha($rim, true);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagesavealpha rim");
  die();
}

// sortie
header("Content-type: image/png");

$r=imagepng($rim, null, 9);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagepng");
  die();
}

