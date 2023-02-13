<?php

require "daoAlerteQualitay.class.php";

class DaoAlerteQualitayMySql implements DaoAlerteQualitay
{

  private $link;

  public function connect()
  {
    $this->link = mysqli_connect("host", "user", 'passwd')
                or die("Impossible de se connecter : " . mysqli_error());
    mysqli_select_db($this->link, "db")
      or die("Impossible selectionner la base : " . mysqli_error());
    mysqli_query($this->link, "SET NAMES 'utf8'");
  }

  public function disconnect()
  {
    mysqli_close($this->link);
  }

  private function getRapporteurs($alerteId)
  {
    $rapporteurs = array();
    $query = <<<"SQL"
           SELECT
           r1.*,
           (SELECT count(*) FROM rapporteur r2 WHERE r2.post_id = r1.post_id) nb
      FROM
      rapporteur r1
      WHERE alerte_qualitay_id = {$alerteId}
      ORDER BY nb DESC, date DESC
      SQL;
    //trigger_error($query);
    $result = mysqli_query($this->link, $query);
    while($row = mysqli_fetch_assoc($result))
    {
      $rapporteur = new Rapporteur($row["id"],
                                   $row["pseudo"],
                                   $row["post_id"],
                                   $row["post_url"],
                                   $row["date"],
                                   $row["initiateur"],
                                   $row["commentaire"]);
      array_push($rapporteurs, $rapporteur);
    }
    return $rapporteurs;
  }

  private function innerGetAlertes($entries = null,
                                   $minimalVotes = null,
                                   $sqlCondition = null)
  {
    $limit = !empty($entries) ? "LIMIT ".$entries : "";
    $nbPosts = !empty($minimalVotes) ? "HAVING count(r2.id) >= ".$minimalVotes : "";
    $where = !empty($sqlCondition) ? "WHERE ".$sqlCondition : "";
    $query = <<<"SQL"
      SELECT
      a.*
      FROM
      alerte_qualitay a INNER JOIN rapporteur r1 ON
      a.id = r1.alerte_qualitay_id AND r1.initiateur = 1 INNER JOIN rapporteur r2 ON
      a.id = r2.alerte_qualitay_id
    {$where}
    GROUP BY a.id
    {$nbPosts}
    ORDER BY max(r1.date) desc
    {$limit}
    SQL;
    //trigger_error($query);
    $result = mysqli_query($this->link, $query);
    $alertes = array();
    while($row = mysqli_fetch_assoc($result))
    {
      $alerte = new Alerte($row["id"], $row["nom"], $row["topic_id"], $row["topic_titre"]);
      $rapporteurs = $this->getRapporteurs($alerte->getId());
      foreach($rapporteurs as $rapporteur)
        $alerte->addRapporteur($rapporteur);
      array_push($alertes, $alerte);
    }
    return $alertes;
  }

  public function getAlerte($alerteId)
  {
    $alerte = null;
    $query = "SELECT * FROM alerte_qualitay WHERE id = '".
           mysqli_real_escape_string($this->link, $alerteId)."'";
    $result = mysqli_query($this->link, $query);
    if($row = mysqli_fetch_assoc($result))
    {
      $alerte = new Alerte($row["id"], $row["nom"], $row["topic_id"], $row["topic_titre"]);
      $rapporteurs = $this->getRapporteurs($alerte->getId());
      foreach($rapporteurs as $rapporteur)
        $alerte->addRapporteur($rapporteur);
    }
    return $alerte;
  }

  public function getAlertes($entries = null, $minimalVotes = null)
  {
    return $this->innerGetAlertes($entries, $minimalVotes);
  }

  public function getAlertesByTopic($topicId)
  {
    $condition = "topic_id = '".mysqli_real_escape_string($this->link, $topicId)."'";
    return $this->innerGetAlertes(null, null, $condition);
  }

  public function getAlertesByIpDuringLastMinute($ip)
  {
    $conditiopn = "TIMESTAMPDIFF(SECOND, r1.date, now()) < 60 and r1.ip = '".
                mysqli_real_escape_string($this->link, $ip)."'";
    return $this->innerGetAlertes(null, null, $conditiopn);
  }

  public function addAlerte(Alerte $alerte)
  {
    $alerteId = $alerte->getId();
    if($alerte->getId() == -1)
    {
      $query = "INSERT INTO alerte_qualitay(nom, topic_id, topic_titre) VALUES(";
      $query .= "'".mysqli_real_escape_string($this->link, $alerte->getNom())."',";
      $query .= "'".mysqli_real_escape_string($this->link, $alerte->getTopicId())."',";
      $query .= "'".mysqli_real_escape_string($this->link, $alerte->getTopicTitre())."')";
      //trigger_error($query);
      $result = mysqli_query($this->link, $query);
      if($result === FALSE)
      {
        trigger_error("daoAlerteQualitayMySql CODE_FAIL_INSERT_DEFAULT alerte");
        return CODE_FAIL_INSERT_DEFAULT;
      }
      else
      {
        $alerteId = mysqli_insert_id($this->link);
      }
    }

    $rapporteurs = $alerte->getRapporteurs();
    $rapporteurs = array_pop($rapporteurs);
    $rapporteur = array_pop($rapporteurs);
    $query = <<<"SQL"
      INSERT INTO rapporteur(alerte_qualitay_id, pseudo, post_id, post_url, date, initiateur
      SQL;
    if($rapporteur->getCommentaire() != null)
      $query .= ", commentaire";
    $query .= ", ip) VALUES(";
    $query .= $alerteId.", ";
    $query .= "'".mysqli_real_escape_string($this->link, $rapporteur->getPseudo())."', ";
    $query .= "'".mysqli_real_escape_string($this->link, $rapporteur->getPostId())."', ";
    $query .= "'".mysqli_real_escape_string($this->link, $rapporteur->getPostUrl())."', ";
    $query .= "'".$rapporteur->getDate()."', ";
    $query .= $rapporteur->isInitiateur() ? "1, " : "0, ";
    $query .= $rapporteur->getCommentaire() != null ?
           "'".mysqli_real_escape_string($this->link, $rapporteur->getCommentaire())."', " :
           "";
    $query .= "'".mysqli_real_escape_string($this->link, $_SERVER["REMOTE_ADDR"])."')";
    //trigger_error($query);
    $result = mysqli_query($this->link, $query);
    if($result === FALSE)
    {
      switch(mysqli_errno($this->link))
      {
      case 1062:
        trigger_error("daoAlerteQualitayMySql CODE_FAIL_INSERT_DUPLICATE_ALERT");
        return CODE_FAIL_INSERT_DUPLICATE_ALERT;
        break;
      default :
        trigger_error("daoAlerteQualitayMySql CODE_FAIL_INSERT_DEFAULT rapporteur");
        return CODE_FAIL_INSERT_DEFAULT;
      };
    }

    return CODE_SUCCESS_INSERT;
  }

  public function deleteAlerte(Alerte $alerte)
  {
    $alerteId = $alerte->getId();
    if(empty($alerteId))
      return;
    $query = "DELETE FROM alerte_qualitay where id = ".$alerte->getId();
    mysqli_query($this->link, $query);
    $query = "DELETE FROM rapporteur where alerte_qualitay_id = ".$alerte->getId();
    mysqli_query($this->link, $query);
  }

}

?>