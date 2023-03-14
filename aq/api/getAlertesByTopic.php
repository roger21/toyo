<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "../errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

//trigger_error("getAlertesByTopic.php");

require "../config/alerteQualitay.config.php";
require "../core/alerte.class.php";
require "../core/rapporteur.class.php";
require "../dao/daoAlerteQualitayMySql.class.php";

$topicId = !empty($_GET["topic_id"]) ? $_GET["topic_id"] : null;

$dom = new DOMDocument("1.0", "utf-8");
$root = $dom->createElement("alertes");
$dom->appendChild($root);

$dao = new daoAlerteQualitayMySql();
$dao->connect();

$alertes = !is_null($topicId) ? $dao->getAlertesByTopic($topicId) : array();
foreach($alertes as $alerte)
{
  $alerteNode = $dom->createElement("alerte");
  $alerteNode->setAttribute("id", $alerte->getId());
  $alerteNode->setAttribute("nom", $alerte->getNom());
  $postsIds = "";

  foreach($alerte->getRapporteurs() as $postId => $rapporteurs)
  {
    foreach($rapporteurs as $rapporteur)
    {
      if($rapporteur->isInitiateur())
      {
        $alerteNode->setAttribute("pseudoInitiateur", $rapporteur->getPseudo());
        $alerteNode->setAttribute("date", date("d-m-Y", strtotime($rapporteur->getDate())));
      }
      $postsIds .= $rapporteur->getPostId().",";
    }
  }
  $alerteNode->setAttribute("postsIds", substr($postsIds, 0, -1));
  $root->appendChild($alerteNode);
}

$dao->disconnect();

header("Content-Type: text/xml");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
echo $dom->saveXml();

?>