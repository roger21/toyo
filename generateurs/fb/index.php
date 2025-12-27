<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$sujet=isset($_GET["t"]) ? $_GET["t"] : "";
$moi=isset($_GET["moi"]);
$pluriel=isset($_GET["pluriel"]);
$not=isset($_GET["not"]);
$smiley=isset($_GET["s"]);

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

// normalise
$sujet=normalizer_normalize($sujet, Normalizer::NFKC);
if($sujet === false){
  trigger_error(__DIR__."/index.php died on normalizer_normalize $sujet");
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
$r=imagealphablending($final, false);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagealphablending final false");
  die();
}
$r=imagesavealpha($final, true);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagesavealpha final");
  die();
}
$trans=imagecolorallocatealpha($final, 0, 0, 0, 127);
if($trans === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocatealpha final trans");
  die();
}
$r=imagefill($final, 0, 0, $trans);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill final trans");
  die();
}
$r=imagecopyresized($final, $thumb, 0, 0, 0, 0, $w, $h, $w, $h);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresized final thumb");
  die();
}

// couleurs
$gris=imagecolorallocate($final, 236, 239, 245);
if($gris === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate final gris");
  die();
}
$bleu=imagecolorallocate($final, 229, 234, 241);
if($bleu === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate final bleu");
  die();
}
$noir=imagecolorallocate($final, 51, 51, 94);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate final noir");
  die();
}

// formes
$r=imagefilledrectangle($final, $w, 5, $wf, 27, $gris);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle final gris");
  die();
}
$r=imagefilledrectangle($final, $w, 28, $wf, $h, $bleu);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefilledrectangle final bleu");
  die();
}

// texte
$r=imagealphablending($final, true);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagealphablending final true");
  die();
}
$r=imagefttext($final, 8, 0, $x, $y, $noir, "./tahoma.ttf", $sujet);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefttext final $sujet");
  die();
}

// sortie
header("Content-type: image/png");

if($smiley){

  $hs=floor($h * 70 / $wf);
  $s=imagecreatetruecolor(70, $hs);
  if($s === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor s");
    die();
  }
  $r=imagealphablending($s, false);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagealphablending s");
    die();
  }
  $r=imagesavealpha($s, true);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagesavealpha s");
    die();
  }
  $r=imagecopyresampled($s, $final, 0, 0, 0, 0, 70, $hs, $wf, $h);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled s final");
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
    trigger_error(__DIR__."/index.php died on imagepng final");
    die();
  }

}

