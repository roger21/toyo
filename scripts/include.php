<?php


require_once("constants.php");


define("SCRIPT_START_MESSAGE", "THE SCRIPT STARTED");
define("SCRIPT_END_MESSAGE", "THE SCRIPT ENDED");

ini_set("date.timezone", "Europe/Paris");


require_once("colors.php");

require_once("errors.php");

require_once("functions.php");


if(!isset($NO_START_END_LOG) || !$NO_START_END_LOG){
  log_error(SCRIPT_START_MESSAGE);
}

function script_end_info(){
  global $NO_START_END_LOG;
  if(!isset($NO_START_END_LOG) || !$NO_START_END_LOG){
    log_error(SCRIPT_END_MESSAGE);
  }
};
register_shutdown_function("script_end_info");


