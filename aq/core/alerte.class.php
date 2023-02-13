<?php

class Alerte
{

  private $id;
  private $nom;
  private $topicId;
  private $topicTitre;
  private $rapporteurs;

  public function __construct($id, $nom, $topicId, $topicTitre)
  {
    $this->id = $id;
    $this->nom = $nom;
    $this->topicId = $topicId;
    $this->topicTitre = $topicTitre;
    $this->rapporteurs = array();
  }

  public function getId()
  {
    return $this->id;
  }

  public function getNom()
  {
    return $this->nom;
  }

  public function getTopicId()
  {
    return $this->topicId;
  }

  public function getTopicTitre()
  {
    return $this->topicTitre;
  }

  public function getRapporteurs()
  {
    return $this->rapporteurs;
  }

  public function addRapporteur(Rapporteur $rapporteur)
  {
    if(!isset($this->rapporteurs[$rapporteur->getPostId()]))
      $this->rapporteurs[$rapporteur->getPostId()] = array();
    array_push($this->rapporteurs[$rapporteur->getPostId()], $rapporteur);
  }

}

?>