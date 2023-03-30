<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text1=isset($_GET["t1"]) ? $_GET["t1"] : "";
$text2=isset($_GET["t2"]) ? $_GET["t2"] : "";
$smiley=isset($_GET["s"]);

$w=123; // largeur de l'image de fond
$h=61; // hauteur de l'image de fond
$t=17; // taille du texte
$dx=95; // décalage de la position du texte en x
$dy1=20; // décalage de la position du texte1 en y
$dy2=43; // décalage de la position du texte2 en y

// images
$fond=imagecreatefrompng("./fond.png");
if($fond === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng fond");
  die();
}
$repeat=imagecreatefrompng("./repeat.png");
if($repeat === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng repeat");
  die();
}
$test=imagecreatetruecolor(1, 1);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
  die();
}

// couleur
$noir=imagecolorallocate($fond, 62, 61, 54);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate noir");
  die();
}

// taille
$pos1=imagefttext($test, $t, 0, 0, 0, $noir, "./unvr57x.ttf", $text1);
if($pos1 === false){
  trigger_error(__DIR__."/index.php died on imagefttext pos1");
  die();
}
$pos2=imagefttext($test, $t, 0, 0, 0, $noir, "./unvr57x.ttf", $text2);
if($pos2 === false){
  trigger_error(__DIR__."/index.php died on imagefttext pos2");
  die();
}
$fw=max($pos1[4], $pos2[4]) + $dx + 5;

// finale
$final=imagecreatetruecolor($fw, $h);
if($final === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor final");
  die();
}
$r=imagecopyresized($final, $fond, 0, 0, 0, 0, $w, $h, $w, $h);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresized fond");
  die();
}
for ($i=$w; $i < $fw; ++$i){
  $r=imagecopyresized($final, $repeat, $i, 0, 0, 0, $i + 1, $h, 1, $h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresized repeat $i");
    die();
  }
}

// textes
$r=imagefttext($final, $t, 0, $dx, $dy1, $noir, "./unvr57x.ttf", $text1);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext text1");
  die();
}
$r=imagefttext($final, $t, 0, $dx, $dy2, $noir, "./unvr57x.ttf", $text2);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext text2");
  die();
}

// sortie
header("Content-type: image/png");

if($smiley){

  $fh=floor($h * 70 / $fw);
  $s=imagecreatetruecolor(70, $fh);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagecopyresampled($s, $final, 0, 0, 0, 0, 70, $fh, $fw, $h);
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

  $r=imagepng($final, null, 9);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagepng");
    die();
  }

}

