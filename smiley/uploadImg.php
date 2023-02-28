<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

//trigger_error("uploadImg.php ".var_export($_REQUEST, true));

function image_upload($name, $path){
  if(!isset($_FILES[$name])){
    trigger_error("uploadImg.php false on name");
    return false;
  }

  if($_FILES[$name]["error"] !== 0){
    trigger_error("uploadImg.php false on error");
    return false;
  }

  $filepath = $path.sha1(microtime(true));

  @move_uploaded_file($_FILES[$name]["tmp_name"], $filepath);

  if(!file_exists($filepath)){
    trigger_error("uploadImg.php false on move_uploaded_file");
    return false;
  }

  $img_info = @getimagesize($filepath);

  if($img_info === false)  {
    @unlink($filepath);
    trigger_error("uploadImg.php false on getimagesize");
    return false;
  }

  $img_types = [IMAGETYPE_BMP,
                IMAGETYPE_GIF,
                IMAGETYPE_JPEG,
                IMAGETYPE_PNG,
                IMAGETYPE_WEBP];
  $exts = [IMAGETYPE_BMP   => ".bmp",
           IMAGETYPE_GIF   => ".gif",
           IMAGETYPE_JPEG  => ".jpg",
           IMAGETYPE_PNG   => ".png",
           IMAGETYPE_WEBP   => ".webp"];

  if(!in_array($img_info[2], $img_types))  {
    @unlink($filepath);
    trigger_error("uploadImg.php false on img_types");
    return false;
  }

  $newpath = $filepath.$exts[$img_info[2]];
  $result = rename($filepath, $newpath);
  if($result === false){
    @unlink($filepath);
    trigger_error("uploadImg.php false on rename");
    return false;
  }

  return $newpath;
}

echo ($path = image_upload("tmpImg", "./temp/")) ? $path : "";

?>