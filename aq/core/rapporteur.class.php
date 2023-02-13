<?php

class Rapporteur
{

  private $id;
  private $pseudo;
  private $postId;
  private $postUrl;
  private $date;
  private $initiateur;
  private $commentaire;

  public function __construct($id,
                              $pseudo,
                              $postId,
                              $postUrl,
                              $date,
                              $initiateur,
                              $commentaire = null)
  {
    $this->id = $id;
    $this->pseudo = str_replace("+", " ", $pseudo);
    $this->postId = $postId;
    $this->postUrl= $postUrl;
    $this->date = $date;
    $this->initiateur = $initiateur == 1;
    $this->commentaire = $commentaire;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getPseudo()
  {
    return $this->pseudo;
  }

  public function getPostId()
  {
    return $this->postId;
  }

  public function getPostUrl()
  {
    return $this->postUrl;
  }

  public function getDate()
  {
    return $this->date;
  }

  public function isInitiateur()
  {
    return $this->initiateur;
  }

  public function getCommentaire()
  {
    return empty($this->commentaire) ? null : $this->commentaire;
  }

}

?>