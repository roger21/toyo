<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

//trigger_error("generateSmiley.php ".var_export($_REQUEST, true));

$targetWidth=70;
$targetHeight=50;

$width=$_GET["width"];
$height=$_GET["height"];
$sharpen=isset($_GET["sharpen"]);
$type=isset($_GET["type"]) && in_array($_GET["type"], ["png", "jpg"], true) ?
     $_GET["type"] : "png";

function size($size){
  if($size > (1024 * 20)){
    return (string)(ceil(($size * 10) / 1024) / 10)." Kio:error";
  }elseif($size > 1024){
    return (string)(ceil(($size * 10) / 1024) / 10)." Kio";
  }else{
    return (string)$size." octets";
  }
}

if($width === "0" || $height === "0"){
  trigger_error("generateSmiley.php died on width heiht 1");
  die();
}

if(($width / $height) > 1.4){
  $coef=$width / $targetWidth;
  $width=$targetWidth;
  $height=round($height / $coef, 0);
}else{
  $coef=$height / $targetHeight;
  $height=$targetHeight;
  $width=round($width / $coef, 0);
}

if($width == 0 || $height == 0){
  trigger_error("generateSmiley.php died on width heiht 2");
  die();
}

if(isset($_GET["fileName"])){
  $fileName=$_GET["fileName"];
}elseif(isset($_GET["url"])){
  $fileName="./temp/".sha1($_GET["url"]);
  if(!file_exists($fileName)){
    $header="User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:102.0) ".
           "Gecko/20100101 Firefox/102.0\r\n".
           "Accept: text/html,application/xhtml+xml,application/xml,".
           "image/gif,image/jpeg,image/png,image/bmp,image/webp,*/*\r\n".
           "Accept-Language: fr-FR,fr,en-US,en\r\n".
           "Accept-Encoding: gzip, deflate, br\r\n".
           "Connection: close\r\n".
           "Pragma: no-cache\r\n".
           "Cache-Control: no-cache\r\n";
    $context=stream_context_create(["http" => ["method" => "GET", "header" => $header],
                                    "ssl" => ["verify_peer" => false,
                                              "verify_peer_name" => false]]);
    $file=file_get_contents($_GET["url"], false, $context);
    if($file === false){
      trigger_error("generateSmiley.php died on file_get_contents");
      die();
    }
    $file=file_put_contents($fileName, $file);
    if($file === false){
      trigger_error("generateSmiley.php died on file_put_contents");
      die();
    }
  }
}else{
  trigger_error("generateSmiley.php died on no fileName nor url");
  die();
}

$infos=getimagesize($fileName);
if($infos === false){
  trigger_error("generateSmiley.php died on getimagesize");
  die();
}

switch($infos[2]){
case IMAGETYPE_BMP:
  $img=imagecreatefrombmp($fileName);
  if($img === false){
    trigger_error("generateSmiley.php died on imagecreatefrombmp");
    die();
  }
  break;
case IMAGETYPE_GIF:
  $img=imagecreatefromgif($fileName);
  if($img === false){
    trigger_error("generateSmiley.php died on imagecreatefromgif");
    die();
  }
  break;
case IMAGETYPE_JPEG:
  $img=imagecreatefromjpeg($fileName);
  if($img === false){
    trigger_error("generateSmiley.php died on imagecreatefromjpeg");
    die();
  }
  break;
case IMAGETYPE_PNG:
  $img=imagecreatefrompng($fileName);
  if($img === false){
    trigger_error("generateSmiley.php died on imagecreatefrompng");
    die();
  }
  break;
case IMAGETYPE_WEBP:
  $img=imagecreatefromwebp($fileName);
  if($img === false){
    trigger_error("generateSmiley.php died on imagecreatefromwebp");
    die();
  }
  break;
default:
  trigger_error("generateSmiley.php died on unkwown image type");
  die();
}

$newImg=imagecreatetruecolor($width, $height);
if($newImg === false){
  trigger_error("generateSmiley.php died on imagecreatetruecolor");
  die();
}

if($type === "png"){
  $result=imagealphablending($newImg, false);
  if($result === false){
    trigger_error("generateSmiley.php died on imagealphablending png");
    die();
  }
  $result=imagesavealpha($newImg, true);
  if($result === false){
    trigger_error("generateSmiley.php died on imagesavealpha png");
    die();
  }
}

$result=imagecopyresampled($newImg,
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
  $sharpenMatrix=[[-1, -1, -1], [-1, 16, -1], [-1, -1, -1]];
  $divisor=8;
  $offset=0;
  $result=imageconvolution($newImg, $sharpenMatrix, $divisor, $offset);
  if($result === false){
    trigger_error("generateSmiley.php died on imageconvolution");
    die();
  }
}

switch($type){
case "png":
  $fileName.=".smiley.png";
  $result=imagepng($newImg, $fileName, 9);
  if($result === false){
    trigger_error("generateSmiley.php died on imagepng");
    die();
  }
  break;
case "jpg":
  $fileName.=".smiley.jpg";
  $result=imagejpeg($newImg, $fileName, 92);
  if($result === false){
    trigger_error("generateSmiley.php died on imagejpeg");
    die();
  }
  break;
}
$size=filesize($fileName);
if($size === false){
  trigger_error("generateSmiley.php died on filesize");
  die();
}

die($fileName.":".size($size));

?>