<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$sujet=isset($_GET["t"]) ? $_GET["t"] : "";
$moi=isset($_GET["moi"]);
$pluriel=isset($_GET["pluriel"]);
$not=isset($_GET["not"]);

$w=28; // largeur du pouce
$h=29; // hauteur du pouce
$x=22; // position du texte en x
$y=22; // position du texte en y

// images
$thumb=imagecreatefrompng($not ? "./thumbd.png" : "./thumbu.png");
if($thumb === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng thumb");
  die();
}
$test=imagecreatetruecolor(1, 1);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
  die();
}

// sujet
if($moi){
  $sujet=$not ? "Je n'aime pas ça" : "J'aime ça";
}else{
  if($not){
    $sujet.=$pluriel ? " n'aiment pas ça" : " n'aime pas ça";
  }else{
    $sujet.=$pluriel ? " aiment ça" : " aime ça";
  }
}

// taille
$noir=imagecolorallocate($test, 0, 0, 0);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate test noir");
  die();
}
$pos=imagefttext($test, 8, 0, 0, 0, $noir, "./tahoma.ttf", $sujet);
if($pos === false){
  trigger_error(__DIR__."/index.php died on imagefttext test sujet");
  die();
}
$wf=$pos[4] + $x + 5;

// finale
$final=imagecreatetruecolor($wf, $h);
if($final === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor final");
  die();
}
$r=imagecopyresized($final, $thumb, 0, 0, 0, 0, $w, $h, $w, $h);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresized thumb");
  die();
}

// couleurs
$gris=imagecolorallocate($final, 236, 239, 245);
if($gris === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate gris");
  die();
}
$bleu=imagecolorallocate($final, 229, 234, 241);
if($bleu === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate bleu");
  die();
}
$noir=imagecolorallocate($final, 51, 51, 94);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate noir");
  die();
}
$rose=imagecolorallocate($final, 255, 0, 255);
if($rose === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate rose");
  die();
}
$r=imagecolortransparent($final, $rose);
if($r === -1){
  trigger_error(__DIR__."/index.php died on imagecolortransparent");
  die();
}

// formes
$r=imagefilledrectangle($final, $w, 0, $wf, 4, $rose);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle rose");
  die();
}
$r=imagefilledrectangle($final, $w, 5, $wf, 27, $gris);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle gris");
  die();
}
$r=imagefilledrectangle($final, $w, 28, $wf, $h, $bleu);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle bleu");
  die();
}

// texte
$r=imagefttext($final, 8, 0, $x, $y, $noir, "./tahoma.ttf", $sujet);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext sujet");
  die();
}

// sortie
header("Content-type: image/png");

$r=imagepng($final, null, 9);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagepng");
  die();
}

