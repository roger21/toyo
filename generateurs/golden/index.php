<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";
require_once "../_include/gif_encoder.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$stars=(isset($_GET["stars"]) && (int)$_GET["stars"] >= 0 &&
        (int)$_GET["stars"] <= 100) ? (int)$_GET["stars"] : 5; // nombre d'étoiles
$golden=(isset($_GET["golden"]) && (int)$_GET["golden"] >= 0 &&
         (int)$_GET["golden"] <= 100) ? (int)$_GET["golden"] : 50; // taux de doré
$wo=isset($_GET["wo"]); // sans texte
$av=(isset($_GET["av"]) && in_array($_GET["av"], ["H", "B"], true)) ?
   $_GET["av"] : "B"; // alignement vertical du texte en haut ou en bas
$ah=(isset($_GET["ah"]) && in_array($_GET["ah"], ["G", "D"], true)) ?
   $_GET["ah"] : "G"; // alignement horizontal du texte à gauche ou à droite
$vert=isset($_GET["vert"]); // texte vertical
$taille=(isset($_GET["taille"]) && (int)$_GET["taille"] >= 1 &&
         (int)$_GET["taille"] <= 3) ? (int)$_GET["taille"] : 2; // taille du texte
$taille+=5; // de 6 à 8
$frames=(isset($_GET["frames"]) && (int)$_GET["frames"] >= 1 &&
         (int)$_GET["frames"] <= 100) ? (int)$_GET["frames"] : 20; // nombre de frames
$v=(isset($_GET["v"]) && (int)$_GET["v"] >= 1 &&
    (int)$_GET["v"] <= 10) ? (int)$_GET["v"] : 8; // vitesse
$dly=82 - (8 * $v); // de 74 à 2 par pas de 8 (i.e. de 740ms à 20ms)

// smiley
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false $s $r");
  die();
}
$width=imagesx($smiley); // largeur du smiley
$height=imagesy($smiley); // hauteur du smiley

// image
$im=imagecreatetruecolor($width, $height);
if($im === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor im");
  die();
}
$blanc=imagecolorallocate($im, 255, 255, 255);
if($blanc === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate im blanc");
  die();
}
$noir=imagecolorallocate($im, 0, 0, 0);
if($noir === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate im noir");
  die();
}
$gold=imagecolorallocate($im, 255, 223, 0); // Golden yellow #FFDF00
if($gold === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate im gold");
  die();
}
$r=imagefill($im, 0, 0, $blanc);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill im blanc");
  die();
}
$r=imagecopyresampled($im, $smiley, 0, 0, 0, 0, $width, $height, $width, $height);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled im smiley");
  die();
}

// texte
if(!$wo){
  $tailles=[6 => ["w" => 32, "h" => 6],
            7 => ["w" => 38, "h" => 7],
            8 => ["w" => 44, "h" => 8]];
  $x=$ah === "G" ? 2 /* gauche */ :
    max($width - $tailles[$taille]["w"] - 2, 2) /* droite */;
  $y=$av === "H" ? $tailles[$taille]["h"] + 2 /* haut */ :
    max($height - 2, $tailles[$taille]["h"] + 2) /* bas */;
  $a=0;
  if($vert){
    $x=$ah === "G" ? $tailles[$taille]["h"] + 2 /* gauche */ :
      max($width - $tailles[$taille]["h"] - 2, $tailles[$taille]["h"] + 2) /* droite */;
    $y=$ah === "G" ?
      ($av === "H" ? min($tailles[$taille]["w"] + 3, $height - 2) /* haut gauche */ :
       $height - 2 /* bas gauche */) :
      ($av === "H" ? 2 /* haut droite */ :
       max($height - $tailles[$taille]["w"] - 2, 2) /* bas droite */);
    $a=$ah === "G" ? 90 : -90;
  }
  $police="./arial.ttf";
  $text="GOLDEN";
  $r=imagefttext($im, $taille, $a, $x - 1, $y - 1, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold -1 -1");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x, $y - 1, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold 0 -1");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x + 1, $y - 1, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold +1 -1");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x + 1, $y, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold +1 0");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x + 1, $y + 1, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold +1 +1");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x, $y + 1, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold 0 + 1");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x - 1, $y + 1, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold -1 +1");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x - 1, $y, $gold, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext gold -1 0");
    die();
  }
  $r=imagefttext($im, $taille, $a, $x, $y, $noir, $police, $text);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefttext noir");
    die();
  }
}

// doré
$fond=imagecreatetruecolor($width, $height);
if($fond === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor fond");
  die();
}
$goldfond=imagecolorallocate($fond, 255, 223, 0); // Golden yellow #FFDF00
if($goldfond === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate fond goldfond");
  die();
}
$r=imagefill($fond, 0, 0, $goldfond);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill fond goldfond");
  die();
}
$r=imagecopymerge($im, $fond, 0, 0, 0, 0, $width, $height, $golden);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopymerge im fond $golden");
  die();
}

// frames
$imgs=[];
$dlys=[];
$nbstars=0;
$starsdata=[];
$steps=2;
$first_frame=imagecreatetruecolor($width, $height);
if($first_frame === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor first_frame");
  die();
}
$frame=imagecreatetruecolor($width, $height);
if($frame === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor frame");
  die();
}
$blanc=imagecolorallocate($frame, 255, 255, 255);
if($blanc === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate frame blanc");
  die();
}

function draw_stars($frame, $i, $fusion=false){
  global $starsdata, $nbstars, $steps, $blanc;
  foreach($starsdata as &$star){
    $x=$star["x"];
    $y=$star["y"];
    $s=$star["s"];
    $t=$s === 0 ? 1 : 3; // taille de l'étoile
    if($s < $steps){
      $r=imageline($frame, $x - $t, $y, $x + $t, $y, $blanc);
      if($r === false){
        trigger_error(__DIR__."/index.php died on imageline frame $s 1 $i $fusion");
        die();
      }
      $r=imageline($frame, $x, $y - $t, $x, $y + $t, $blanc);
      if($r === false){
        trigger_error(__DIR__."/index.php died on imageline frame $s 2 $i $fusion");
        die();
      }
      ++$star["s"];
      if($star["s"] === $steps){
        --$nbstars;
      }
    }
  }
}

function save_frame($frame, $i, $fusion=false){
  global $imgs, $dlys, $dly;
  ob_start();
  $r=imagegif($frame);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagegif frame $i $fusion");
    die();
  }
  if($fusion){
    $imgs[0]=ob_get_clean();
  }else{
    $imgs[]=ob_get_clean();
    $dlys[]=$dly;
  }
}

for($i=0; $i < $frames; ++$i){

  // creation des nouvelles étoiles
  if($i < $frames && $nbstars < $stars){
    $newstars=rand(1, $stars - $nbstars);
    for($j=0; $j < $newstars; ++$j){
      $starsdata[]=[
        "x" => rand(0, $width - 1),
        "y" => rand(0, $height - 1),
        "s" => 0,
      ];
    }
    $nbstars+=$newstars;
  }

  // réinitialisation de la frame
  $r=imagecopy($frame, $im, 0, 0, 0, 0, $width, $height);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopy frame im $i");
    die();
  }

  // dessin des etoiles
  draw_stars($frame, $i);

  // sauvegarde de la premiere frame
  if($i === 0){
    $r=imagecopy($first_frame, $frame, 0, 0, 0, 0, $width, $height);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagecopy first_frame frame");
      die();
    }
  }

  // enregistrement de la frame
  save_frame($frame, $i);

}

// fusion de la dernière frame sur la première
draw_stars($first_frame, 0, true);
save_frame($first_frame, 0, true);

$gif=new GIFEncoder($imgs, $dlys, 0, 2, 0, 0, 0, 0, "bin" );

// sortie
header("Content-type:image/gif");

echo $gif->GetAnimation();

