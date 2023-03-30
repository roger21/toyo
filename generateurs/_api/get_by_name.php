<?php

require_once "../_include/errors.php";

//trigger_error("get_by_name.php ".var_export($_REQUEST, true));

$result="";

$pattern=isset($_GET["pattern"]) ? strtolower($_GET["pattern"]) : "";

if($pattern !== ""){

  $pattern_len = strlen($pattern);

  $smileys=file("./smileys.txt", FILE_IGNORE_NEW_LINES);

  foreach($smileys as $smiley){

    if(strncmp($smiley, $pattern, $pattern_len) === 0){

      $result.=$smiley.";";

    }

  }

}

echo trim($result, ";");

