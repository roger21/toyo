<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

//trigger_error("index.php ".$_SERVER["REMOTE_ADDR"]);
//trigger_error("index.php ".var_export($_REQUEST, true));

// Nettoyage des vieilles images de plus d'une heure
$dir = "./temp/";
if($dh = opendir($dir))
{
  while(($file = readdir($dh)) !== false)
  {
    if($file !== "." && $file !== ".." && $file !== ".htaccess")
    {
      $filepath = $dir.$file;
      if((date("U") - date("U", filemtime($filepath))) > 3600){
        //trigger_error("nettoyage ".$filepath);
        @unlink($filepath);
      }
    }
  }
}
closedir($dh);

?><!DOCTYPE html>
<html lang="fr">
  <head>
    <title>:: The AvatarHelper@HFR by ToYonos ::</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-language" content="fr">
    <meta http-equiv="Pragma" content="no-cache">
    <meta name="Robots" content="index, follow">
    <meta name="keywords" content="hfr toyonos avatar helper">
    <meta name="description" content="L'AvatarHelper qui vous aidera à créer vos avatars pour le Forum HFR">
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/imgareaselect-default.css">
    <script type="text/javascript" src="scripts/jquery.min.js"></script>
    <script type="text/javascript" src="scripts/jquery.imgareaselect.min.js"></script>
    <script type="text/javascript" src="scripts/ajaxupload.js"></script>
    <script type="text/javascript" src="scripts/scripts.js.php"></script>
  </head>
  <body>
    <div id="input_area">
      <div id="input_area_div">
        <input id="upload_button" type="button" value="Uploader une image">
        <p>ou URL de l&apos;image</p>
        <input id="url_input" type="url" spellcheck="false">
        <input id="go_button" type="button" value="GO">
      </div>
      <div id="wait_upload_div">
        <p>Upload en cours...</p>
        <img src="img/wait.gif" alt="wait" title="Upload en cours...">
      </div>
    </div>
    <div id="image_area">
      <img id="image" src="" title="Faites votre sélection à l&apos;aide de votre souris">
      <div id="infos_area">
        <div id="infos_area_div">Faites votre sélection à l&apos;aide de votre souris</div>
        <div id="wait_generate_div">
          <p>Génération en cours...</p>
          <img src="img/wait.gif" alt="wait" title="Génération en cours...">
        </div>
        <div id="preview_area">
          <div>
            <p>Aperçu de l&apos;avatar</p>
            <img id="image_preview" src="" alt="preview">
            <label id="lock_ratio_label" for="lock_ratio">Verrouiller le ratio</label>
            <input id="lock_ratio" type="checkbox">
            <input id="ratio_left" type="text" maxlength="2" spellcheck="false" value="3" disabled="disabled" pattern="[1-9]([0-9])?" title="entre 1 et 99">
            <p>:</p>
            <input id="ratio_right" type="text" maxlength="2" spellcheck="false" value="2" disabled="disabled" pattern="[1-9]([0-9])?" title="entre 1 et 99">
            <label id="sharpen_label" for="sharpen">Sharpen</label>
            <input id="sharpen" type="checkbox">
          </div>
          <input id="bbcode_input" type="text" readonly="readonly">
        </div>
      </div>
    </div>
  </body>
</html>