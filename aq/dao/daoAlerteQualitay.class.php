<?php

interface DaoAlerteQualitay
{

  public function getAlerte($alerteId);

  public function getAlertes($entries = null, $minimalVotes = null);

  public function getAlertesByTopic($topicId);

  public function getAlertesByIpDuringLastMinute($ip);

  public function addAlerte(Alerte $alerte);

  public function deleteAlerte(Alerte $alerte);

}

?>