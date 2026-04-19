<?php

{

  ini_set("display_errors", "0");
  ini_set("display_startup_errors", "0");
  ini_set("error_log", "errors_hash_db.log");
  ini_set("error_log_mode", 0600);
  ini_set("error_reporting", "-1");
  ini_set("html_errors", "0");
  ini_set("log_errors", "1");
  ini_set("log_errors_max_len", "0");

  trigger_error("hash_db.php");

  return;

  require "../config/alerteQualitay.config.server.php";

  $link=mysqli_connect(AQ_DB_HOST, AQ_DB_USER, AQ_DB_PASS)
       or die("mysqli_connect : Impossible de se connecter à la base");
  mysqli_select_db($link, AQ_DB_BASE)
    or die("Impossible selectionner la base : " . mysqli_error($link));
  mysqli_query($link, "SET NAMES 'utf8'");

  $query="SELECT id, ip FROM rapporteur";
  trigger_error($query);
  $result=mysqli_query($link, $query);
  while($row=mysqli_fetch_assoc($result)){
    $ip_hash=hash_hmac("sha3-256", strtolower(trim($row["ip"])), PROJECT_PUBLIC_HASH_KEY);
    $query_hash="UPDATE rapporteur SET ip='".$ip_hash."' WHERE id=".$row["id"];
    trigger_error($query_hash);
    $result_hash=mysqli_query($link, $query_hash);
  }

  mysqli_close($link);

}