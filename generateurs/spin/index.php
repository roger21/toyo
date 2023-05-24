<?php

require_once "../_include/errors.php";
require_once "../_include/get_smiley.php";
require_once "../_include/gif_encoder.php";

//trigger_error(__DIR__."/index.php ".var_export($_REQUEST, true));

// paramètres
$s=isset($_GET["s"]) ? $_GET["s"] : ""; // smiley
$r=isset($_GET["r"]) ? $_GET["r"] : 0; // rang
$mode=(isset($_GET["mode"]) && (int)$_GET["mode"] >= 0 && (int)$_GET["mode"] <= 1) ?
     (int)$_GET["mode"] : 0; // mode
$rayonx=(isset($_GET["rayonx"]) && (int)$_GET["rayonx"] >= 0 &&
         (int)$_GET["rayonx"] <= 1000) ? (int)$_GET["rayonx"] : 120; // rayon x
$rayony=(isset($_GET["rayony"]) && (int)$_GET["rayony"] >= 0 &&
         (int)$_GET["rayony"] <= 1000) ? (int)$_GET["rayony"] : 120; // rayon y
$angle=(isset($_GET["angle"]) && (int)$_GET["angle"] >= 0 &&
        (int)$_GET["angle"] <= 359) ? (int)$_GET["angle"] : 0; // angle
$asteps=(isset($_GET["asteps"]) && (int)$_GET["asteps"] >= 0 &&
         (int)$_GET["asteps"] <= 100) ? (int)$_GET["asteps"] : 24; // steps rotation
$rsteps=(isset($_GET["rsteps"]) && (int)$_GET["rsteps"] >= 0 &&
         (int)$_GET["rsteps"] <= 100) ? (int)$_GET["rsteps"] : 10; // steps spirale
$branches=(isset($_GET["branches"]) && (int)$_GET["branches"] >= 0 &&
           (int)$_GET["branches"] <= 100) ? (int)$_GET["branches"] : 8; // branches
$rofl=isset($_GET["rofl"]); // rofl
$fixe=isset($_GET["fixe"]); // fixe
$center=isset($_GET["center"]); // centré
$forced_center=false;
if($rayonx === 0 || $rayony === 0 || $asteps === 0 || $branches === 0 ||
   ($mode === 1 && $rsteps === 0)){
  $forced_center=true;
}
$v=(isset($_GET["v"]) && (int)$_GET["v"] >= 1 && (int)$_GET["v"] <= 10) ?
           (int)$_GET["v"] : 6; // vitesse
$dly=52 - (($v + 15) * 2); // de 20 à 2 par pas de 2 (i.e. de 200ms à 20ms)

// smiley
$smiley=get_smiley($s, $r);
if($smiley === false){
  //trigger_error(__DIR__."/index.php died on smiley === false $s $r");
  die();
}
$ws=imagesx($smiley); // largeur du smiley
$hs=imagesy($smiley); // hauteur du smiley

// smiley rotaté à 45° pour la dimension
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
$maxx=max($ws, $hs, $l) + ($rayonx * 2) + 2; // taille du gif en x
$maxy=max($ws, $hs, $l) + ($rayony * 2) + 2; // taille du gif en y

// frames
$imgs=[];
$dlys=[];
function add_frame($asmiley, $iasteps, $irayonx, $irayony){
  global $imgs, $dlys, $smiley, $ws, $hs, $maxx, $maxy, $dly,
    $angle, $branches, $rofl, $fixe, $center, $forced_center;
  // frame
  $frame=imagecreatetruecolor($maxx, $maxy);
  if($frame === false){
    trigger_error(__DIR__."/index.php died on imagecreatetruecolor frame ".
                  "$asmiley $iasteps $irayonx $irayony");
    die();
  }
  $fond=imagecolorallocate($frame, 255, 255, 255);
  if($fond === false){
    trigger_error(__DIR__."/index.php died on imagecolorallocate frame ".
                  "$asmiley $iasteps $irayonx $irayony");
    die();
  }
  $r=imagefill($frame, 0, 0, $fond);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagefill frame ".
                  "$asmiley $iasteps $irayonx $irayony");
    die();
  }
  // smileys
  if(!$forced_center){
    for($i=0; $i < $branches; ++$i){
      $im=imagecreatetruecolor($ws, $hs);
      if($im === false){
        trigger_error(__DIR__."/index.php died on imagecreatetruecolor im ".
                      "$asmiley $iasteps $irayonx $irayony");
        die();
      }
      $fond=imagecolorallocate($im, 255, 255, 255);
      if($fond === false){
        trigger_error(__DIR__."/index.php died on imagecolorallocate im ".
                      "$asmiley $iasteps $irayonx $irayony");
        die();
      }
      $r=imagefill($im, 0, 0, $fond);
      if($r === false){
        trigger_error(__DIR__."/index.php died on imagefill im ".
                      "$asmiley $iasteps $irayonx $irayony");
        die();
      }
      $r=imagecopyresampled($im, $smiley, 0, 0, 0, 0, $ws, $hs, $ws, $hs);
      if($r === false){
        trigger_error(__DIR__."/index.php died on imagecopyresampled im smiley ".
                      "$asmiley $iasteps $irayonx $irayony");
        die();
      }
      $abranche=round($i * 360 / $branches);
      $apos=$rofl ? $iasteps - $abranche : $asmiley - $abranche;
      $arot=round(rad2deg(atan2(sin(deg2rad($apos)) * $irayony,
                                cos(deg2rad($apos)) * $irayonx)));
      $a=$fixe ? $asmiley : ($rofl ? $arot + $asmiley : $arot - $angle);
      $im=imagerotate($im, $a, $fond);
      if($im === false){
        trigger_error(__DIR__."/index.php died on imagerotate im ".
                      "$asmiley $iasteps $irayonx $irayony");
        die();
      }
      $wr=imagesx($im);
      $hr=imagesy($im);
      $x=round(($maxx - $wr) / 2);
      $y=round(($maxy - $hr) / 2);
      $dx=round($irayonx * sin(deg2rad($iasteps - $abranche)));
      $dy=round($irayony * cos(deg2rad($iasteps - $abranche)));
      $r=imagecopyresampled($frame, $im, $x - $dx, $y - $dy, 0, 0, $wr, $hr, $wr, $hr);
      if($r === false){
        trigger_error(__DIR__."/index.php died on imagecopyresampled frame im ".
                      "$asmiley $iasteps $irayonx $irayony");
        die();
      }
    }
  }
  // center
  if($center || $forced_center){
    $im=imagecreatetruecolor($ws, $hs);
    if($im === false){
      trigger_error(__DIR__."/index.php died on imagecreatetruecolor im center ".
                    "$asmiley $iasteps $irayonx $irayony");
      die();
    }
    $fond=imagecolorallocate($im, 255, 255, 255);
    if($fond === false){
      trigger_error(__DIR__."/index.php died on imagecolorallocate im center ".
                    "$asmiley $iasteps $irayonx $irayony");
      die();
    }
    $r=imagefill($im, 0, 0, $fond);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagefill im center ".
                    "$asmiley $iasteps $irayonx $irayony");
      die();
    }
    $r=imagecopyresampled($im, $smiley, 0, 0, 0, 0, $ws, $hs, $ws, $hs);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagecopyresampled im smiley center ".
                    "$asmiley $iasteps $irayonx $irayony");
      die();
    }
    $im=imagerotate($im, $asmiley, $fond);
    if($im === false){
      trigger_error(__DIR__."/index.php died on imagerotate im center ".
                    "$asmiley $iasteps $irayonx $irayony");
      die();
    }
    $wr=imagesx($im);
    $hr=imagesy($im);
    $x=round(($maxx - $wr) / 2);
    $y=round(($maxy - $hr) / 2);
    $r=imagecopyresampled($frame, $im, $x, $y, 0, 0, $wr, $hr, $wr, $hr);
    if($r === false){
      trigger_error(__DIR__."/index.php died on imagecopyresampled frame im center ".
                    "$asmiley $iasteps $irayonx $irayony");
      die();
    }
  }
  ob_start();
  $r=imagegif($frame);
  if($r === false){
    trigger_error(__DIR__."/index.php died on imagegif frame ".
                  "$asmiley $iasteps $irayonx $irayony");
    die();
  }
  $imgs[]=ob_get_clean();
  $dlys[]=$dly;
}
// plus petit commun multiple
function ppcm($a, $b, $c){
  $n=$a * $b * $c;
  for($i=max($a, $b, $c); $i <= $n; ++$i){
    if($i % $a === 0 && $i % $b === 0 && $i % $c === 0){
      $n=$i;
      break;
    }
  }
  return $n;
}
$n=1;
if($forced_center){
  $n=$rofl ? 8 : ($asteps === 0 ? 1 : ($fixe ? 1 : $asteps));
}else{
  if($mode === 0){
    $n=ppcm($asteps, 1, $rofl ? 8 : 1);
  }
  if($mode === 1){
    $n=ppcm($asteps, $rsteps * 2, $rofl ? 8 : 1);
  }
}

/*
  trigger_error(var_export(["mode" => $mode,
  "rayonx" => $rayonx,
  "rayony" => $rayony,
  "angle" => $angle,
  "asteps" => $asteps,
  "rsteps" => $rsteps,
  "branches" => $branches,
  "rofl" => $rofl,
  "fixe" => $fixe,
  "center" => $center,
  "forced_center" => $forced_center,
  "n" => $n], true));
*/

for($i=0; $i < $n; ++$i){
  $iasteps=$asteps === 0 ? -$angle : -$angle - round($i * 360 / $asteps);
  $asmiley=$rofl ? -$i * 45 : ($fixe ? -$angle : $iasteps);
  $irayonx=$rayonx;
  $irayony=$rayony;
  if($mode === 1 && $rsteps !== 0){
    $krs=intdiv($i, $rsteps);
    $rrs=$i % $rsteps;
    $irayonx=round(($krs % 2 === 0 ? $rrs : $rsteps - $rrs) * $rayonx / $rsteps);
    $irayony=round(($krs % 2 === 0 ? $rrs : $rsteps - $rrs) * $rayony / $rsteps);
  }
  add_frame($asmiley, $iasteps, $irayonx, $irayony);
}

$gif=new GIFEncoder($imgs, $dlys, 0, 2, 252, 254, 252, 0, "bin");

// sortie
header("Content-type:image/gif");

echo $gif->GetAnimation();

