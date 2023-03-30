<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";
require_once "../_include/gif_encoder.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$dx=(isset($_GET["dx"]) && (int)$_GET["dx"] >= 0 && (int)$_GET["dx"] <= 100) ?
   (int)$_GET["dx"] : 10; // delta x
$fy=(isset($_GET["fy"]) && (int)$_GET["fy"] >= 1 && (int)$_GET["fy"] <= 10) ?
   (int)$_GET["fy"] : 2; // facteur y
$rofl=isset($_GET["rofl"]); // rofl
$v=(isset($_GET["v"]) && (int)$_GET["v"] >= 1 && (int)$_GET["v"] <= 10) ?
  (int)$_GET["v"] : 6; // vitesse
$dly=52 - (($v + 15) * 2); // de 20 à 2 (i.e. de 200ms à 20ms)

// smiley
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false");
  die();
}
$ws=imagesx($smiley); // largeur du smiley
$hs=imagesy($smiley); // hauteur du smiley

// smiley écrasé
$s_ws=ceil($ws * 1.5); // largeur du smiley écrasé
$s_hs=ceil($hs * 0.6); // hauteur du smiley écrasé
$s_smiley=imagecreatetruecolor($s_ws, $s_hs);
if($s_smiley === false){
  trigger_error(__DIR__."/index.php died on imagecreatetruecolor s_smiley");
  die();
}
$fond=imagecolorallocate($s_smiley, 255, 255, 255);
if($fond === false){
  trigger_error(__DIR__."/index.php died on imagecolorallocate s_smiley");
  die();
}
$r=imagefill($s_smiley, 0, 0, $fond);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagefill s_smiley");
  die();
}
$r=imagecopyresampled($s_smiley, $smiley, 0, 0, 0, 0, $s_ws, $s_hs, $ws, $hs);
if($r === false){
  trigger_error(__DIR__."/index.php died on imagecopyresampled s_smiley");
  die();
}

// smiley rotaté à 45°
$l=0;
if($rofl){
  $fond=imagecolorat($smiley, 0, 0);
  if($fond === false){
    trigger_error(__DIR__."/index.php died on imagecolorat test");
    die();
  }
  $test=imagerotate($smiley, 45, $fond);
  if($test === false){
    trigger_error(__DIR__."/index.php died on imagerotate test");
    die();
  }
  $l=imagesx($test); // côté du smiley rotaté ($test est un carré)
}

// frames
$imgs=[];
$dlys=[];
function add_frame($i, $smiley, $ws, $hs, $x, $y, $shadow, $angle, $absolute_y=false){
  global $imgs, $dlys, $w, $h, $maxw, $maxh, $gdy, $dly;
  $frame=imagecreatetruecolor($w, $h);
  if($frame === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor frame $i");
    die();
  }
  $fond=imagecolorallocate($frame, 255, 255, 255);
  if($fond === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate frame $i");
    die();
  }
  $r=imagefill($frame, 0, 0, $fond);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefill frame $i");
    die();
  }
  $ldx=ceil(($maxw - $ws) / 2);
  $ldy=ceil(($maxh - $hs) / 2);
  if($shadow){
    $wsha=ceil($ws * $shadow);
    $hsha=ceil($hs * $shadow / 4);
    // n'affiche l'ombre que si l'image ne la touche pas (possible avec un fy bas)
    if($y + $maxh < $h - $hsha - $gdy - 1){
      $noir=imagecolorallocate($frame, 0, 0, 0);
      if($noir === false){
        trigger_error(__DIR__."/index.php died on imagecolorallocate frame shadow $i");
        die();
      }
      $r=imagefilledellipse($frame,
                            $x + $ldx + ceil($ws / 2),
                            $h - ceil($hsha / 2) - $gdy - 1,
                            $wsha, $hsha, $noir);
      if($r === false){
        trigger_error(__DIR__."/index.php died on imagefilledellipse frame shadow $i");
        die();
      }
    }
  }
  $im=imagecreatetruecolor($ws, $hs);
  if($im === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor frame im $i");
    die();
  }
  $fond=imagecolorallocate($im, 255, 255, 255);
  if($fond === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate frame im $i");
    die();
  }
  $r=imagefill($im, 0, 0, $fond);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefill frame im $i");
    die();
  }
  $r=imagecopyresampled($im, $smiley, 0, 0, 0, 0, $ws, $hs, $ws, $hs);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled frame im $i");
    die();
  }
  if($angle % 360){
    $im=imagerotate($im, $angle, $fond);
    if($im === false){
      trigger_error(__DIR__."/index.php died on imagerotate frame im $i");
      die();
    }
    $ws=imagesx($im);
    $hs=imagesy($im);
    $ldx=ceil(($maxw - $ws) / 2);
    $ldy=ceil(($maxh - $hs) / 2);
  }
  $ldy=$absolute_y ? 0 : $ldy;
  $r=imagecopyresampled($frame, $im, $x + $ldx, $y + $ldy, 0, 0, $ws, $hs, $ws, $hs);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagecopyresampled frame $i");
    die();
  }
  ob_start();
  $r=imagegif($frame);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagegif frame $i");
    die();
  }
  $imgs[]=ob_get_clean();
  $dlys[]=$dly;
}
$nbf=17; // nombre de frames aller / retour
$half_nbf=8; // nombre de frames monté / descente
$maxw=max($s_ws, $l); // largeur maximale du smiley
$maxh=max($hs, $l); // hauteur maximale du smiley
$gdy=ceil(($maxh - $hs) / 2); // baseline (interlinked) pour l'ombre et le smiley écrasé
$w=$maxw + (($nbf - 1) * $dx); // largeur du gif
$h=$maxh + ceil(($half_nbf ** 2) * $fy); // hauteur du gif (courbe parabolique en x²)
// frames aller
for($i=0; $i < $nbf; ++$i){
  $j=$i < $half_nbf ? $half_nbf - $i : $i - $half_nbf; // 8 7 6 5 4 3 2 1 0 1 2 3 4 5 6 7 8
  add_frame("aller $i", $smiley, $ws, $hs, $i * $dx, ceil($fy * ($j ** 2)),
            0.5 + ((($j ** 2) / ($half_nbf ** 2)) * 0.5),
            $rofl ? -($i * 45) : 0);
}
// smiley écrasé
add_frame("s 0", $s_smiley, $s_ws, $s_hs, ($nbf - 1) * $dx, $h - $s_hs - $gdy, 0, 0, true);
// frames retour
for($i=0; $i < $nbf; ++$i){
  $j=$i < $half_nbf ? $half_nbf - $i : $i - $half_nbf; // 8 7 6 5 4 3 2 1 0 1 2 3 4 5 6 7 8
  add_frame("retour $i", $smiley, $ws, $hs, ($nbf - 1 - $i) * $dx, ceil($fy * ($j ** 2)),
            0.5 + ((($j ** 2) / ($half_nbf ** 2)) * 0.5),
            $rofl ? ($i * 45) : 0);
}
// smiley écrasé
add_frame("s 1", $s_smiley, $s_ws, $s_hs, 0, $h - $s_hs - $gdy, 0, 0, true);

$gif=new GIFEncoder($imgs, $dlys, 0, 2, 252, 254, 252, 0, "bin");

// sortie
header("Content-type:image/gif");

echo $gif->GetAnimation();

