<?php

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
ini_set("error_log", "./errors/errors.log");
ini_set("error_log_mode", 0600);
ini_set("error_reporting", "-1");
ini_set("html_errors", "0");
ini_set("log_errors", "1");
ini_set("log_errors_max_len", "0");

require "config/alerteQualitay.config.php";
require "core/alerte.class.php";
require "core/rapporteur.class.php";
require "dao/daoAlerteQualitayMySql.class.php";

$entries = ((isset($_GET[PARAM_RSS_ENTRIES]) &&
             ctype_digit($_GET[PARAM_RSS_ENTRIES])) ?
            $_GET[PARAM_RSS_ENTRIES] :
            DEFAULT_ENTRIES);
$enableSmilies = (isset($_GET[PARAM_RSS_ENABLE_SMILIES]) ?
                  strtolower($_GET[PARAM_RSS_ENABLE_SMILIES]) === "true" :
                  strtolower(DEFAULT_ENABLE_SMILIES) === "true");
$minimalVotes = (isset($_GET[PARAM_RSS_MINIMAL_VOTES]) &&
                 ctype_digit($_GET[PARAM_RSS_MINIMAL_VOTES]) ?
                 $_GET[PARAM_RSS_MINIMAL_VOTES] :
                 DEFAULT_MINIMAL_VOTES);

$dom = new DOMDocument("1.0", "utf-8");
$dom->formatOutput = true;

$root = $dom->createElement("rss");
$root->setAttribute("version", "2.0");
$root->setAttribute("xmlns:atom", "http://www.w3.org/2005/Atom");
$dom->appendChild($root);

$channel = $dom->createElement("channel");
$root->appendChild($channel);

$title = $dom->createElement("title");
$title->appendChild($dom->createTextNode("Alertes Qualitaÿ sur HFR"));
$channel->appendChild($title);

$description = $dom->createElement("description");
$description->appendChild($dom->createTextNode("Pour ne rien rater du meilleur d'HFR, en toutes circonstances !"));
$channel->appendChild($description);

$link = $dom->createElement("link");
$link->appendChild($dom->createTextNode("http://forum.hardware.fr/"));
$channel->appendChild($link);

$language = $dom->createElement("language");
$language->appendChild($dom->createTextNode("fr"));
$channel->appendChild($language);

$copyright = $dom->createElement("copyright");
$copyright->appendChild($dom->createTextNode("Copyright 2009 ToYonos"));
$channel->appendChild($copyright);

$lastBuildDate = $dom->createElement("lastBuildDate");
$lastBuildDate->appendChild($dom->createTextNode(date(DATE_RFC2822)));
$channel->appendChild($lastBuildDate);

$image = $dom->createElement("image");
$titleimage = $dom->createElement("title");
$titleimage->appendChild($dom->createTextNode("Alertes Qualitaÿ sur HFR"));
$image->appendChild($titleimage);
$url = $dom->createElement("url");
$url->appendChild($dom->createTextNode(URL_BASE."img/logo.gif"));
$image->appendChild($url);
$link = $dom->createElement("link");
$link->appendChild($dom->createTextNode("http://forum.hardware.fr/"));
$image->appendChild($link);
$channel->appendChild($image);

$atom = $dom->createElement("atom:link");
$atom->setAttribute("href", URL_BASE."rss.php");
$atom->setAttribute("rel", "self");
$atom->setAttribute("type", "application/rss+xml");
$channel->appendChild($atom);

function safe($string)
{
  return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8", true);
}

$dao = new daoAlerteQualitayMySql();
$dao->connect();

$alertes = $dao->getAlertes($entries, $minimalVotes);
foreach($alertes as $alerte)
{
  $item = $dom->createElement("item");

  $titlealerte = $dom->createElement("title");
  $titlealerte->appendChild($dom->createTextNode($alerte->getNom()));
  $item->appendChild($titlealerte);

  $nbRapporteurs = 0;
  foreach($alerte->getRapporteurs() as $postId => $rapporteurs) $nbRapporteurs += count($rapporteurs);
  $mainLink = null;
  $mainDate = null;

  $descriptionContent = "<p>Une qualitaÿ a été detectée sur <b>".safe($alerte->getTopicTitre())."</b></p>";
  $descriptionContent .= "<p>Elle a été signalée <b>".$nbRapporteurs." fois</b> par les membres suivants :</p>";
  foreach($alerte->getRapporteurs() as $postId => $rapporteurs)
  {
    $descriptionContent .= "<p>&nbsp;&nbsp;&nbsp;<b>#</b> ".count($rapporteurs)." fois via <a href=\"".safe($rapporteurs[0]->getPostUrl())."\">ce post</a> : </p>";
    $descriptionContent .= "<ul>";
    foreach($rapporteurs as $rapporteur)
    {
      $descriptionContent .= "<li>";

      $descriptionContent .= "[".date('\l\e d-m-Y à H:i:s', strtotime($rapporteur->getDate()))."] ";
      if($rapporteur->isInitiateur())
      {
        $descriptionContent .= "<b>".safe($rapporteur->getPseudo())." (initiateur)</b>";
        $mainLink = $rapporteur->getPostUrl();
        $mainDate = $rapporteur->getDate();
      }
      else
      {
        $descriptionContent .= safe($rapporteur->getPseudo());
      }

      $commentaire = $rapporteur->getCommentaire() == null ? " : (pas de commentaire)" : " : ".safe($rapporteur->getCommentaire());
      if($enableSmilies)
      {
        foreach($smilies as $code => $regexp) $commentaire = preg_replace($regexp, '<img src="'.safe(URL_SMILIES.$code.'.gif').'" alt="'.$code.'" title="'.$code.'" />', $commentaire);
        foreach($smilies2 as $code => $regexp) $commentaire = preg_replace($regexp, '$1<img src="'.safe(URL_SMILIES2.$code.'.gif').'" alt="'.$code.'" title="'.$code.'" />', $commentaire);
        $commentaire = preg_replace('/\[:([^\]]+):(\d+)]/i', '<img src="'.safe(URL_SMILIES_PERSO.'$2/$1.gif').'" alt="[:$1:$2]" title="[:$1:$2]" />', $commentaire);
        $commentaire = preg_replace('/\[:([^\]:]+)]/i', '<img src="'.safe(URL_SMILIES_PERSO.'$1.gif').'" alt="[:$1]" title="[:$1]" />', $commentaire);
      }
      $descriptionContent .= $commentaire;

      $descriptionContent .= "</li>";
    }
    $descriptionContent .= "</ul>";
  }

  $link = $dom->createElement("link");
  $link->appendChild($dom->createTextNode($mainLink));
  $item->appendChild($link);

  $guid = $dom->createElement("guid");
  $guid->appendChild($dom->createTextNode($mainLink));
  $item->appendChild($guid);

  $pubDate = $dom->createElement("pubDate");
  $pubDate->appendChild($dom->createTextNode(date(DATE_RFC2822, strtotime($mainDate))));
  $item->appendChild($pubDate);

  $description = $dom->createElement("description");
  $description->appendChild($dom->createCDATASection($descriptionContent));
  $item ->appendChild($description);

  $channel->appendChild($item);
}

$dao->disconnect();

header("Content-Type: application/rss+xml;charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
echo $dom->saveXml();

?>