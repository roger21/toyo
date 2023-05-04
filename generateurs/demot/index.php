<?php

require_once "../_include/errors.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$pseudal=isset($_GET["p"]) ? str_replace("\u{200B}", "", trim($_GET["p"])) : ""; // pseudal
$titre=isset($_GET["t"]) ? strtoupper($_GET["t"]) : ""; // titre
$texte=isset($_GET["c"]) ? $_GET["c"] : ""; // texte

if($pseudal !== ""){

  // profil
  $url="https://forum.hardware.fr/profilebdd.php?config=hfr.inc&pseudo=";
  $profile=@file_get_contents($url.rawurlencode(trim($pseudal)));
  $re="#<div\s*class=\"avatar_center\"\s*style=\"clear:both\"\s*><img\s*src=\"([^\"]*)\"#";
  if(preg_match($re, $profile, $r) !== 1){
    //trigger_error(__DIR__."/index.php died on preg_match profile $pseudal);
    die();
  }

  // avatar
  $avatar=@imagecreatefromgif($r[1]);
  if($avatar === false){
    $avatar=@imagecreatefrompng($r[1]);
  }
  if($avatar === false){
    $avatar=@imagecreatefromjpeg($r[1]);
  }
  if($avatar === false){
    //trigger_error(__DIR__."/index.php died on imagecreate avatar $pseudal");
    die();
  }
  $wa=imagesx($avatar); // largeur de l'avatar
  $ha=imagesy($avatar); // hauteur de l'avatar

  // normalise
  $texte=normalizer_normalize($texte, Normalizer::NFKC);
  if($texte === false){
    trigger_error(__DIR__."/index.php died on normalizer_normalize $texte");
    die();
  }

  // dimensions
  $policetitre="./times.ttf";
  $tailletitre=20;
  $policetexte="./arial.ttf";
  $tailletexte=8;
  $wt=0; // largeur du titre
  $lines=[]; // lingnes du texte
  $nbl=0; // nombre de lignes de texte
  $poss=[]; // dimensions des lignes du texte
  $we=0; // largeur du texte
  $lh=14; // hauteur des lignes du texte
  $test=imagecreatetruecolor(1, 1);
  if($test === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor test");
    die();
  }
  $blanc=imagecolorallocate($test, 255, 255, 255);
  if($blanc === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate test blanc");
    die();
  }
  // titre
  if($titre !== ""){
    $pos=imagefttext($test, $tailletitre, 0, 0, 0, $blanc, $policetitre, $titre);
    if($pos === false){
      trigger_error(__DIR__."/index.php died on imagefttext test $titre");
      die();
    }
    $wt=$pos[4] - $pos[0];
  }
  // texte
  if($texte !== ""){
    $lines=preg_split("/\r?\n/", $texte);
    if($lines === false){
      trigger_error(__DIR__."/index.php died on preg_split $texte");
      die();
    }
    $nbl=count($lines);
    foreach($lines as $i => $line){
      $pos=imagefttext($test, $tailletexte, 0, 0, 0, $blanc, $policetexte, $line);
      if($pos === false){
        trigger_error(__DIR__."/index.php died on imagefttext test $i $line");
        die();
      }
      $poss[]=$pos;
      $we=max($we, $pos[4] - $pos[0]);
    }
  }
  $w=max($wa, $wt, $we) + 50; // largeur de l'image
  $h=$ha + ($nbl * $lh) + 60; // hauteur de l'image

  // image
  $im=imagecreatetruecolor($w, $h);
  if($im === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor im");
    die();
  }
  $blanc=imagecolorallocate($im, 255, 255, 255);
  if($blanc === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate im blanc");
    die();
  }
  $xa=floor(($w - $wa) / 2); // position de l'avatar en x
  $ya=15; // position de l'avatar en y
  // bordure blanche autour de l'avatar
  $r=imagerectangle($im, $xa - 2, $ya - 2, $xa + $wa + 1, $ya + $ha + 1, $blanc);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagerectangle im blanc");
    die();
  }
  // fond blanc derière l'avatar ... meh
  /*
    $r=imagefilledrectangle($im, $xa, $ya, $xa + $wa - 1, $ya + $ha - 1, $blanc);
    if($r === false){
    trigger_error(__DIR__."/index.php died on imagefilledrectangle im blanc");
    die();
    }
  */
  // avatar
  $r=imagecopy($im, $avatar, $xa, $ya, 0, 0, $wa, $ha);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopy im avatar");
    die();
  }

  // titre
  if($titre !== ""){
    $x=floor(($w - $wt) / 2);
    $y=$ha + 45;
    $r=imagefttext($im, $tailletitre, 0, $x, $y, $blanc, $policetitre, $titre);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagefttext im $titre");
      die();
    }
  }

  // texte
  if($texte !== ""){
    foreach($lines as $i => $line){
      $x=floor(($w - ($poss[$i][4] - $poss[$i][0])) / 2);
      $y=$ha + 48 + (($i + 1) * $lh);
      $r=imagefttext($im, $tailletexte, 0, $x, $y, $blanc, $policetexte, $line);
      if($r === false){
        trigger_error(__DIR__."/index.php died on imagefttext im $i $line");
        die();
      }
    }
  }

  // sortie
  header("Content-type: image/png");

  $r=imagepng($im, null, 9);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagepng im");
    die();
  }

}

