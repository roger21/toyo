<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "../errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

trigger_error("addAlerte.php");

require "../config/alerteQualitay.config.php";
require "../config/blackList.config.php";
require "../core/alerte.class.php";
require "../core/rapporteur.class.php";
require "../dao/daoAlerteQualitayMySql.class.php";

function fail($code)
{
  global $dao;
  $dao->disconnect();
  header("Content-Type: text/plain");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Pragma: no-cache");
  die($code);
}

function startsWith($haystack, $needle)
{
  return !strncmp($haystack, $needle, strlen($needle));
}

function safe($string)
{
  return htmlspecialchars($string, ENT_NOQUOTES | ENT_SUBSTITUTE, "UTF-8", true);
}

$ip = $_SERVER["REMOTE_ADDR"];
$args = & $_POST;
$alerteMandatoryParameters = array("nom", "topic_id", "topic_titre");
$rapporteurMandatoryParameters = array("pseudo", "post_id", "post_url");
$from = empty($args["from"]) ? "" : " from : ".$args["from"];
$from_mail = empty($args["from"]) ? "" : "\r\n\r\nfrom : ".safe($args["from"]);

if(in_array($ip, $blackList))
{
  trigger_error("[AQ] Nouvelle tentative ip : ".$ip.$from);
  mail(MAIL_ADDRESS, "[AQ] Nouvelle tentative ip",
       "ip : ".safe($ip).$from_mail);
  header("HTTP/1.1 403 Forbidden");
  header("Content-Length: 0");
  return;
}

$dao = new daoAlerteQualitayMySql();
$dao->connect();

$alertes = $dao->getAlertesByIpDuringLastMinute($ip);
if(count($alertes) >= 3)
{
  foreach($alertes as $alerte)
  {
    trigger_error("[AQ] deleteAlerte : id : ".$alerte->getId().
                  " nom : ".$alerte->getNom()." topic : ".$alerte->getTopicTitre());
    $dao->deleteAlerte($alerte);
  }
  trigger_error("[AQ] Nouveau blocage ip : ".$ip.$from);
  mail(MAIL_ADDRESS, "[AQ] Nouveau blocage ip",
       "ip : ".safe($ip).$from_mail);
  $blackList[count($blackList)] = $ip;
  $filename = "../config/blackList.config.php";
  $file = fopen($filename, "w");
  fwrite($file, "<?php\n\n\$blackList = ".var_export($blackList, TRUE).";\n\n?>");
  fclose($file);
  die();
}

$newAlerte = null;

if(!empty($args["alerte_qualitay_id"]) && $args["alerte_qualitay_id"] != -1)
{
  $currentAlerte = $dao->getAlerte($args["alerte_qualitay_id"]);
  if(is_null($currentAlerte))
  {
    trigger_error("[AQ] CODE_FAIL_INSERT_INVALID_ALERT ip : ".$ip.$from);
    fail(CODE_FAIL_INSERT_INVALID_ALERT);
  }
  $newAlerte = new Alerte($currentAlerte->getId(),
                          $currentAlerte->getNom(),
                          $currentAlerte->getTopicId(),
                          $currentAlerte->getTopicTitre());
}
else
{
  foreach($alerteMandatoryParameters as $param)
  {
    if(empty($args[$param]))
    {
      trigger_error("[AQ] CODE_FAIL_INSERT_MISSING_PARAMETER alerte ip : ".$ip.$from);
      fail(CODE_FAIL_INSERT_MISSING_PARAMETER);
    }
  }
  $newAlerte = new Alerte(-1, $args["nom"], $args["topic_id"], $args["topic_titre"]);
}

foreach($rapporteurMandatoryParameters as $param)
{
  if(empty($args[$param]))
  {
    trigger_error("[AQ] CODE_FAIL_INSERT_MISSING_PARAMETER rapporteur ip : ".$ip.$from);
    fail(CODE_FAIL_INSERT_MISSING_PARAMETER);
  }
}

if(!startsWith($args["post_url"], "https://forum.hardware.fr"))
{
  trigger_error("[AQ] url post pas en https://forum.hardware.fr ip : ".$ip.$from);
  header("HTTP/1.1 403 Forbidden");
  header("Content-Length: 0");
  return;
}

$newRapporteur = new Rapporteur(-1,
                                $args["pseudo"],
                                $args["post_id"],
                                $args["post_url"],
                                date("Y-m-d H:i:s"),
                                $newAlerte->getId() == -1 ? 1 : 0,
                                isset($args["commentaire"]) ? $args["commentaire"] : null);
$newAlerte->addRapporteur($newRapporteur);
$code = $dao->addAlerte($newAlerte);

$dao->disconnect();

trigger_error("[AQ] Nouvelle AQ code : ".$code." ip : ".$ip.$from);
mail(MAIL_ADDRESS, "[AQ] Nouvelle AQ",
     "code : ".safe($code).
     "\r\n\r\nip : ".safe($ip).$from_mail.MAIL_LINKS);
header("Content-Type: text/plain");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
echo $code;

?>