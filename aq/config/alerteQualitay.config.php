<?php

DEFINE("URL_BASE", "");

DEFINE("MAIL_ADDRESS", "");

DEFINE("MAIL_LINKS", "");

DEFINE("PARAM_RSS_ENTRIES", "entries");
DEFINE("PARAM_RSS_ENABLE_SMILIES", "enable_smilies");
DEFINE("PARAM_RSS_MINIMAL_VOTES", "minimal_votes");

DEFINE("DEFAULT_ENTRIES", "25");
DEFINE("DEFAULT_ENABLE_SMILIES", "true");
DEFINE("DEFAULT_MINIMAL_VOTES", "1");

DEFINE("CODE_SUCCESS_INSERT", "1");
DEFINE("CODE_FAIL_INSERT_DEFAULT", "-1");
DEFINE("CODE_FAIL_INSERT_INVALID_ALERT", "-2");
DEFINE("CODE_FAIL_INSERT_MISSING_PARAMETER", "-3");
DEFINE("CODE_FAIL_INSERT_DUPLICATE_ALERT", "-4");

DEFINE("URL_SMILIES", "http://forum-images.hardware.fr/icones/smilies/");
DEFINE("URL_SMILIES2", "http://forum-images.hardware.fr/icones/");
DEFINE("URL_SMILIES_PERSO", "http://forum-images.hardware.fr/images/perso/");

/*************************************/

$smilies = array(
  "gratgrat" => "/:gratgrat:/i",
  "ange" => "/:ange:/i",
  "benetton" => "/:benetton:/i",
  "bic" => "/:bic:/i",
  "bounce" => "/:bounce:/i",
  "bug" => "/:bug:/i",
  "crazy" => "/:crazy:/i",
  "cry" => "/:cry:/i",
  "dtc" => "/:dtc:/i",
  "eek" => "/:eek:/i",
  "eek2" => "/:eek2:/i",
  "evil" => "/:evil:/i",
  "fou" => "/:fou:/i",
  "foudtag" => "/:foudtag:/i",
  "fouyaya" => "/:fouyaya:/i",
  "fuck" => "/:fuck:/i",
  "gun" => "/:gun:/i",
  "hebe" => "/:hebe:/i",
  "heink" => "/:heink:/i",
  "hello" => "/:hello:/i",
  "hot" => "/:hot:/i",
  "int" => "/:int:/i",
  "jap" => "/:jap:/i",
  "kaola" => "/:kaola:/i",
  "lol" => "/:lol:/i",
  "love" => "/:love:/i",
  "mad" => "/:mad:/i",
  "mmmfff" => "/:mmmfff:/i",
  "na" => "/:na:/i",
  "non" => "/:non:/i",
  "ouch" => "/:ouch:/i",
  "ouimaitre" => "/:ouimaitre:/i",
  "pfff" => "/:pfff:/i",
  "pouah" => "/:pouah:/i",
  "pt1cable" => "/:pt1cable:/i",
  "sarcastic" => "/:sarcastic:/i",
  "sleep" => "/:sleep:/i",
  "sol" => "/:sol:/i",
  "spamafote" => "/:spamafote:/i",
  "spookie" => "/:spookie:/i",
  "sum" => "/:sum:/i",
  "sweat" => "/:sweat:/i",
  "vomi" => "/:vomi:/i",
  "wahoo" => "/:wahoo:/i",
  "whistle" => "/:whistle:/i",
);

$smilies2 = array(
  "confused" => "/([^\[]|^):\?\?:/i",
  "smile" => "/([^\[]|^):\)/i",
  "frown" => "/([^\[]|^):\(/i",
  "redface" => "/([^\[]|^):o/i",
  "biggrin" => "/([^\[]|^):D/i",
  "wink" => "/([^\[]|^);\)/i",
  "tongue" => "/([^\[]|^):p/i",
  "ohill" => "/([^\[]|^):\"\(/i",
  "ohwell" => "/([^\[]|^)(:\/)(?!\/)/i",
);

?>
