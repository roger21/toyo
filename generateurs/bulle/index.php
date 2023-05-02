<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";
require_once "../_include/wp_normalize.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$text=isset($_GET["t"]) ? $_GET["t"] : ""; // texte
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$delta=(isset($_GET["delta"]) && (int)$_GET["delta"] >= 0 &&
        (int)$_GET["delta"] <= 100) ? (int)$_GET["delta"] : 0; // delta
$flip=isset($_GET["flip"]); // inverser
$police=(isset($_GET["police"]) &&
         in_array($_GET["police"], ["carter", "cartoon", "tintin"], true)) ?
       $_GET["police"] : "cartoon"; // police
$taille=(isset($_GET["taille"]) && (int)$_GET["taille"] >= 1 &&
         (int)$_GET["taille"] <= 3) ? (int)$_GET["taille"] : 1; // taille

// polices
$polices=["carter" => ["taille" => [1 => "8", 2 => "9", 3 => "10"]],
          "cartoon" => ["taille" => [1 => "6", 2 => "7", 3 => "8"]],
          "tintin" => ["taille" => [1 => "10", 2 => "11", 3 => "12"]]];
$policefile="./".$police.".ttf";
$t=$polices[$police]["taille"][$taille];
// ajout de décallages en fonction de la police et de la taille
$ddx=$police === "carter" ? 1 : 0;
$ddb=$police === "cartoon" && $taille !== 1 ? 1 : 0;

// normalise
$text=normalizer_normalize($text, Normalizer::NFKC);
if($text === false){
  trigger_error(__DIR__."/index.php died on normalizer_normalize $text");
  die();
}
if($police === "cartoon"){
  $text=remove_accents($text);
}

// smiley
$ws=0; // largeur du smiley
$hs=0; // hauteur du smiley
$dx=0; // décalage horizontal pour le smiley
$dy=0; // décalage vertical pour le smiley
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false $s $r");
}
if($smiley !== false){
  $ws=imagesx($smiley);
  $hs=imagesy($smiley);
  $dx=$ws + 3;
  $dy=$hs - floor($hs / 5);
  $delta=0;
}

// dimensions
$wt=0; // largeur du texte
$ht=0; // hauteur du texte
$bt=0; // baseline du texte
if($text !== ""){
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
  $pos=imagefttext($test, $t, 0, 0, 0, $noir, $policefile, $text);
  if($pos === false){
    trigger_error(__DIR__."/index.php died on imagefttext test $text");
    die();
  }
  $wt=$pos[4] - $pos[0];
  $ht=$pos[1] - $pos[5];
  $bt=-$pos[5];
}
$wf=$dx + 7 + $wt + $ddx + 4; // largeur finale de la bulle
$hb=4 + $ddb + $ht + 6; // hauteur de la bulle
$hf=$hb + $dy + $delta; // hauteur finale de la bulle

// bulle
$bulle=imagecreatetruecolor($wf, $hf);
if($bulle === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor bulle");
  die();
}
$blanc=imagecolorallocate($bulle, 255, 255, 255);
if($blanc === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate bulle blanc");
  die();
}
$noir=imagecolorallocate($bulle, 0, 0, 0);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate bulle noir");
  die();
}
$r=imagealphablending($bulle, false);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagealphablending bulle false");
  die();
}
$r=imagesavealpha($bulle, true);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagesavealpha bulle");
  die();
}
$trans=imagecolorallocatealpha($bulle, 0, 0, 0, 127);
if($trans === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocatealpha bulle trans");
  die();
}
$r=imagefill($bulle, 0, 0, $trans);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill bulle trans");
  die();
}
// coins de la bulle
$tl=imagecreatefrompng("./tl.png"); // top left
if($tl === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng tl");
  die();
}
$r=imagecopyresampled($bulle, $tl, $dx, 0, 0, 0, 7, 4, 7, 4);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled bulle tl");
  die();
}
$tr=imagecreatefrompng("./tr.png"); // top right
if($tr === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng tr");
  die();
}
$r=imagecopyresampled($bulle, $tr, $wf - 4, 0, 0, 0, 4, 4, 4, 4);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled bulle tr");
  die();
}
$br=imagecreatefrompng("./br.png"); // bottom right
if($br === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng br");
  die();
}
$r=imagecopyresampled($bulle, $br, $wf - 4, $hb - 6, 0, 0, 4, 6, 4, 6);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled bulle br");
  die();
}
$bl=imagecreatefrompng("./bl.png"); // bottom left
if($bl === false){
  trigger_error(__DIR__."/index.php died on imagecreatefrompng bl");
  die();
}
$r=imagecopyresampled($bulle, $bl, $dx, $hb - 6, 0, 0, 7, 6, 7, 6);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled bulle bl");
  die();
}
if($text !== ""){
  // bordures
  $r=imageline($bulle, $dx + 7, 0, $wf - 5, 0, $noir); // bordure du haut
  if($r === false){
    trigger_error(__DIR__."/index.php died on imageline bulle noir bordure du haut");
    die();
  }
  $r=imageline($bulle, $wf - 1, 4, $wf - 1, $hb - 7, $noir); // bordure de droite
  if($r === false){
    trigger_error(__DIR__."/index.php died on imageline bulle noir bordure de droite");
    die();
  }
  $r=imageline($bulle, $dx + 7, $hb - 3, $wf - 5, $hb - 3, $noir); // bordure du bas
  if($r === false){
    trigger_error(__DIR__."/index.php died on imageline bulle noir bordure du bas");
    die();
  }
  $r=imageline($bulle, $dx + 3, 4, $dx + 3, $hb - 7, $noir); // bordure de gauche
  if($r === false){
    trigger_error(__DIR__."/index.php died on imageline bulle noir bordure de gauche");
    die();
  }
  // fond blanc
  $r=imagefilledrectangle($bulle, $dx + 7, 1, $wf - 5, $hb - 4, $blanc);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefilledrectangle bulle blanc 1");
    die();
  }
  $r=imagefilledrectangle($bulle, $dx + 4, 4, $wf - 2, $hb - 7, $blanc);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefilledrectangle bulle blanc 1");
    die();
  }
}

// inversion
if($flip){
  $r=imageflip($bulle, IMG_FLIP_HORIZONTAL);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imageflip bulle");
    die();
  }
}

// smiley
if($smiley !== false){
  $x=$flip ? $wf - $ws : 0;
  $r=imagecopyresampled($bulle, $smiley, $x, $hf - $hs, 0, 0, $ws, $hs, $ws, $hs);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled bulle smiley");
    die();
  }
}

// texte
if($text !== ""){
  $r=imagealphablending($bulle, true);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagealphablending bulle true");
    die();
  }
  $x=$flip ? 4 : $dx + 7;
  $r=imagefttext($bulle, $t, 0, $x, 4 + $ddb + $bt, $noir, $policefile, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext test $text");
    die();
  }
}

// sortie
header("Content-type: image/png");

$r=imagepng($bulle, null, 9);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagepng");
  die();
}

