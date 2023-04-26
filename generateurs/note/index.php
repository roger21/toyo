<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$n=(isset($_GET["n"]) && preg_match("/^[0-9]{1,2}$/", $_GET["n"]) === 1) ?
  (string)(int)$_GET["n"] : ""; // numérateur
$d=(isset($_GET["d"]) && preg_match("/^[0-9]{1,2}$/", $_GET["d"]) === 1) ?
  (string)(int)$_GET["d"] : ""; // dénominateur

$t=20; // taille des chiffres
$a=15; // angle des chiffres
$xn=9; // position du numérateur en x
$yn=24; // position du numérateur en y
$xd=16; // position du dénominateur en x
$yd=47; // position du dénominateur en y

// images
$fond=imagecreatefrompng("./fond.png");
if($fond === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng fond");
  die();
}

// couleur
$rouge=imagecolorallocate($fond, 241, 104, 130);
if($rouge === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate rouge");
  die();
}

// forme
$r=imageline($fond, 9, 28, 39, 19, $rouge);
if($r === false){
  trigger_error(__DIR__."/index.php died on imageline rouge");
  die();
}

// chiffres
if(strlen($n) > 0){
  $dxn=strlen($n) === 1 ? 5 : 0;
  $dyn=strlen($n) === 1 ? -2 : 0;
  $r=imagefttext($fond, $t, $a, $xn + $dxn, $yn + $dyn, $rouge, "./plumbdl.ttf", $n);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext numérateur");
    die();
  }
}
if(strlen($d) > 0){
  $dxd=strlen($d) === 1 ? 5 : 0;
  $dyd=strlen($d) === 1 ? -2 : 0;
  $r=imagefttext($fond, $t, $a, $xd + $dxd, $yd + $dyd, $rouge, "./plumbdl.ttf", $d);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext dénominateur");
    die();
  }
}

// sortie
header("Content-type: image/png");

$r=imagepng($fond, null, 9);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagepng");
  die();
}

