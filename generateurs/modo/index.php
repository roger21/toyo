<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$t=isset($_GET["t"]) ? $_GET["t"] : ""; // texte
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$i=(isset($_GET["i"]) && (int)$_GET["i"] >= 1 && (int)$_GET["i"] <= 5) ?
  (int)$_GET["i"] : 1; // icônes
$ts=isset($_GET["ts"]) && ctype_digit($_GET["ts"]) ? $_GET["ts"] : 0; // timestamp

$dyd=0; // décallage verticale de la date en fonction des icônes
$dyt=0; // décallage verticale du texte en fonction des icônes
if($i !== 1){
  if($i === 2){
    $dyd=5;
    $dyt=4;
  }else{ // 3 4 5
    $dyd=3;
    $dyt=2;
  }
}

$lines=preg_split("/\r?\n/", $t); // lingnes du texte
if($lines === false){
  trigger_error(__DIR__."/index.php died on preg_split $t");
  die();
}
$nbl=count($lines); // nombre de lignes
$lle=$lines[$nbl - 1] === ""; // est-ce que la dernière ligne est vide

// smiley
$ws=0; // largeur du smiley
$hs=0; // hauteur du smiley
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false $s $r");
}
if($smiley !== false){
  $ws=imagesx($smiley);
  $hs=imagesy($smiley);
}

$wf=597; // largeur du fond
$hf=75 + $dyt; // hauteur du fond
$wt=455; // largeur disponible de base pour le texte
$ht=40; // hauteur disponible de base pour le texte
$hl=17; // hauteur d'une ligne de texte
$bsl=4; // hauteur de la baseline (interlinked) pour le smiley
$dws=4; // espace entre le texte et le smiley
$wa=30; // largeur du bouton d'alerte
$ha=24 + $dyt; // hauteur du bouton d'alerte
$sd=8; // taille du texte pour la date
$st=10; // taille du texte pour le texte
$ff="./a".$i.".png"; // fichier de l'image de fond
$fa="./b".$i.".png"; // fichier de l'image du bouton d'alerte
$xline=132; // position en x de la ligne à gauche du contenu
$yline=23 + $dyt; // position en y de le ligne au dessus du contenu
$dl=5; // distance entre la ligne au dessus du contenu et les bords
$x=$xline + $dl; // position du texte en x pour la date et le texte
$yd=18 + $dyd; // position du texte en y pour la date
$hc=(($nbl - 1) * $hl) + max($hs + ($lle ? 0 : $bsl), $hl); // hauteur du contenu
// décallage verticale du texte en fonction du contenu
$dyt2=$hc < $ht ? ceil(($ht - $hc) / 2) : 0;
$h=max($ht, $hc) + $hf - $ht; // hauteur de l'image
$yt=43 + $dyt + $dyt2; // position du texte en y pour le texte

// image de test
$test=imagecreatetruecolor(1, 1);
if($test === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
  die();
}
$noir=imagecolorallocate($test, 0, 0, 0);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate noir test");
  die();
}
$poss=[];
$maxwl=0;
foreach($lines as $j => $line){
  $pos=imagefttext($test, $st, 0, 0, 0, $noir, "./verdana.ttf", $line);
  if($pos === false){
    trigger_error(__DIR__."/index.php died on imagefttext test $j");
    die();
  }
  $poss[]=$pos;
  $wl=$pos[4] - $pos[0]; // largeur de la ligne
  if(($j === $nbl - 1) && ($smiley !== false) && ($ws > 0)){
    $wl=($line !== "") ? $wl + $dws + $ws : $ws;
  }
  $maxwl=max($maxwl, $wl, $wt);
}
$w=$maxwl + $wf - $wt; // largeur de l'image

// images
$im=imagecreatetruecolor($w, $h);
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor");
  die();
}
$fond=imagecreatefrompng($ff);
if($fond === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng $ff");
  die();
}
$alerte=imagecreatefrompng($fa);
if($alerte === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng $fa");
  die();
}

// couleurs
$noir=imagecolorallocate($im, 0, 0, 0);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate noir");
  die();
}
$rose=imagecolorallocate($im, 255, 238, 238);
if($rose === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate rose");
  die();
}
$gris1=imagecolorallocate($im, 192, 192, 192);
if($gris1 === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate gris1");
  die();
}
$gris2=imagecolorallocate($im, 119, 119, 119);
if($gris2 === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate gris2");
  die();
}

// remplissage
$r=imagefill($im, 0, 0, $rose); // fond rose
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill");
  die();
}
// image de fond
$r=imagecopyresampled($im, $fond, 0, 0, 0, 0, $wf, $hf, $wf, $hf);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled fond");
  die();
}
// image du bouton d'alerte
$r=imagecopyresampled($im, $alerte, $w - $wa, 0, 0, 0, $wa, $ha, $wa, $ha);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled alerte");
  die();
}
$r=imageline($im, 0, 0, $w - 1, 0, $gris1); // bordure du haut
if($r === -1){
  trigger_error(__DIR__."/index.php died on imageline bordure du haut");
  die();
}
$r=imageline($im, 0, 0, 0, $h - 1, $gris1); // bordure de gauche
if($r === -1){
  trigger_error(__DIR__."/index.php died on imageline bordure de gauche");
  die();
}
$r=imageline($im, $xline, 0, $xline, $h - 1, $gris1); // ligne à gauche du contenu
if($r === -1){
  trigger_error(__DIR__."/index.php died on imageline ligne à gauche du contenu");
  die();
}
$r=imageline($im, $w - 1, 0, $w - 1, $h - 1, $gris1); // bordure de droite
if($r === -1){
  trigger_error(__DIR__."/index.php died on imageline bordure de droite");
  die();
}
$r=imageline($im, 0, $h - 1, $w - 1, $h - 1, $gris1); // bordure du bas
if($r === -1){
  trigger_error(__DIR__."/index.php died on imageline bordure du bas");
  die();
}
// ligne au dessus du contenu
$r=imageline($im, $xline + $dl, $yline, $w - 1 - $dl, $yline, $gris2);
if($r === -1){
  trigger_error(__DIR__."/index.php died on imageline ligne au dessus du contenu");
  die();
}

// date
date_default_timezone_set("Europe/Paris");
$date="Posté le ".date("d-m-Y à H:i:s", $ts ? $ts : time());
$r=imagefttext($im, $sd, 0, $x, $yd, $noir, "./verdana.ttf", $date);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext date");
  die();
}

// texte
foreach($lines as $j => $line){
  $yl=$yt + ($j * $hl);
  if(($j === $nbl - 1) && ($smiley !== false) && ($hs > 0)){
    $yl+=max($hs + $bsl - $hl, 0);
  }
  if($line !== ""){
    $r=imagefttext($im, $st, 0, $x, $yl, $noir, "./verdana.ttf", $line);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagefttext $j");
      die();
    }
  }
}

// smiley
if($smiley !== false){
  $xs=$x + ($lle ? 0 : (($poss[$nbl - 1][4] - $poss[$nbl - 1][0]) + $dws));
  $ys=$yt + (($nbl - 1) * $hl) - ($hl - $bsl);
  $r=imagecopyresampled($im, $smiley, $xs, $ys, 0, 0, $ws, $hs, $ws, $hs);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled smiley");
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

