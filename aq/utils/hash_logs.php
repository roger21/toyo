#!/usr/bin/php
<?php

{

  ini_set("display_errors", "0");
  ini_set("display_startup_errors", "0");
  ini_set("error_log", "errors_hash_logs.log");
  ini_set("error_log_mode", 0600);
  ini_set("error_reporting", "-1");
  ini_set("html_errors", "0");
  ini_set("log_errors", "1");
  ini_set("log_errors_max_len", "0");

  trigger_error("hash_logs.php");

  require "../config/alerteQualitay.config.server.php";

  function hash_replace($m){
    return $m[1].hash_hmac("sha3-256", strtolower(trim($m[2])), PROJECT_PUBLIC_HASH_KEY).$m[3];
  };

  //$file=file_get_contents("errors.server.log");
  $file=file_get_contents("errors.server.2026_03_27.log");

  $new_file=preg_replace_callback("/([^0-9])((?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})([^0-9])/", "hash_replace", $file);

  //file_put_contents("errors.server.log.1", $new_file);
  file_put_contents("errors.server.2026_03_27.log.1", $new_file);

}