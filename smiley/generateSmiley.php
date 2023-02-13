<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

//trigger_error("generateSmiley.php ".$_SERVER["REMOTE_ADDR"]);
//trigger_error("generateSmiley.php ".var_export($_REQUEST, true));

$targetWidth = 70;
$targetHeight = 50;

$width = $_GET["width"];
$height = $_GET["height"];
$sharpen = isset($_GET["sharpen"]);
$fileName = isset($_GET["fileName"]) ? $_GET["fileName"] : $_GET["url"];

if($width === "0" || $height === "0"){
  trigger_error("generateSmiley.php died on width heiht 1");
  die();
}

if(($width / $height) > 1.4){
  $coef = $width / $targetWidth;
  $width = $targetWidth;
  $height = round($height / $coef, 0);
}else{
  $coef = $height / $targetHeight;
  $height = $targetHeight;
  $width = round($width / $coef, 0);
}

if($width == 0 || $height == 0){
  trigger_error("generateSmiley.php died on width heiht 2");
  die();
}

$newImg = imagecreatetruecolor($width, $height);
$img = @imagecreatefromgif($fileName);
if($img === false)
  $img = @imagecreatefrompng($fileName);
if($img === false)
  $img = @imagecreatefromjpeg($fileName);
if($img === false){
  trigger_error("generateSmiley.php died on imgcreatefrom");
  die();
}

$result = imagecopyresampled($newImg,
                             $img,
                             0,
                             0,
                             $_GET["x1"],
                             $_GET["y1"],
                             $width,
                             $height,
                             $_GET["width"],
                             $_GET["height"]);
if($result === false){
  trigger_error("generateSmiley.php died on imagecopyresampled");
  die();
}

if($sharpen){
  $sharpenMatrix = array(array(-1,-1,-1), array(-1,16,-1), array(-1,-1,-1));
  $divisor = 8;
  $offset = 0;
  $result = imageconvolution($newImg, $sharpenMatrix, $divisor, $offset);
  if($result === false){
    trigger_error("generateSmiley.php died on imageconvolution");
    die();
  }
}

$fileName = (isset($_GET["fileName"]) ? $_GET["fileName"] : "./temp/".sha1($_GET["url"])).
  ".smiley.png";

$result = imagepng($newImg, $fileName, 9);
if($result === false){
  trigger_error("generateSmiley.php died on imagepng");
  die();
}

die($fileName);

?>