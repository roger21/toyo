<?php

require_once "../_include/errors.php";

//trigger_error("smiley.php ".var_export($_REQUEST, true));

$images=["./biggrin.gif", "./miam.gif", "./redface.gif", "./smile.gif"];

// image
$im=imagecreatefromgif($images[array_rand($images)]);
if($im === false){
  trigger_error("smiley.php died on imagecreatefromgif");
  die();
}

// sortie
header("Expires: Thu, 1 Jan 1970 00:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: image/gif");

$r=imagegif($im, null);
if($r === false){
  trigger_error("smiley.php  died on imagegif");
  die();
}

