<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text=isset($_GET["t"]) ? $_GET["t"] : ""; // texte
$smiley=isset($_GET["s"]); // smiley

$wi=101; // largeur de la tête
$hi=135; // hauteur de la tête
$taille=18; // taille du texte
$police="./impact.ttf"; // police
$lh=27; // hauteur des lignes

// normalise
$text=normalizer_normalize($text, Normalizer::NFKC);
if($text === false){
  trigger_error(__DIR__."/index.php died on normalizer_normalize $text");
  die();
}

// dimensions
$lines=[]; // lingnes du texte
$nbl=0; // nombre de lignes
$poss=[]; // dimensions des lignes
$wt=0; // largeur du texte
if($text !== ""){
  $lines=preg_split("/\r?\n/", $text);
  if($lines === false){
    trigger_error(__DIR__."/index.php died on preg_split $text");
    die();
  }
  $nbl=count($lines);
  $test=imagecreatetruecolor(1, 1);
  if($test === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
    die();
  }
  $noir=imagecolorallocate($test, 0, 0, 0);
  if($noir === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate test noir");
    die();
  }
  foreach($lines as $i => $line){
    $pos=imagefttext($test, $taille, 0, 0, 0, $noir, $police, $line);
    if($pos === false){
      trigger_error(__DIR__."/index.php died on imagefttext test $i $line");
      die();
    }
    $poss[]=$pos;
    $wt=max($wt, $pos[4] - $pos[0]);
  }
}
$w=max($wi, $wt) + 30; // largeur de l'image
$h=($nbl * $lh) + $hi + 40; // hauteur de l'image

// image
$im=imagecreatetruecolor($w, $h);
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor im");
  die();
}
$noir=imagecolorallocate($im, 0, 0, 0);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate im noir");
  die();
}
$jaune=imagecolorallocate($im, 253, 237, 2);
if($jaune === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate im jaune");
  die();
}
$r=imagefill($im, 0, 0, $noir); // bordure noire
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill im noir");
  die();
}
$r=imagefilledrectangle($im, 2, 2, $w - 3, $h - 3, $jaune); // fond jaune
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle im jaune");
  die();
}

// tête
$tete=imagecreatefrompng("./tete.png");
if($tete === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng tete");
  die();
}
$x=floor(($w - $wi) / 2);
$y=$h - $hi - 15;
$r=imagecopyresampled($im, $tete, $x, $y, 0, 0, $wi, $hi, $wi, $hi);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled im tete");
  die();
}

// texte
if($text !== ""){
  foreach($lines as $i => $line){
    $x=floor(($w - ($poss[$i][4] - $poss[$i][0])) / 2);
    $y=(($i + 1) * $lh) + 10;
    $r=imagefttext($im, $taille, 0, $x, $y, $noir, $police, $line);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagefttext im $i $line");
      die();
    }
  }
}

// sortie
header("Content-type: image/png");

if($smiley){

  $ws=70;
  $hs=50;
  if($w / $h < 1.4){
    $ws=floor($w * 50 / $h);
  }else{
    $hs=floor($h * 70 / $w);
  }
  $s=imagecreatetruecolor($ws, $hs);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagecopyresampled($s, $im, 0, 0, 0, 0, $ws, $hs, $w, $h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled s im");
    die();
  }
  $r=imagepng($s, null, 9);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagepng s");
    die();
  }

}else{

  $r=imagepng($im, null, 9);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagepng im");
    die();
  }

}

