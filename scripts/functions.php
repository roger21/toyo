<?php


function exec_command($command){
  $value=exec($command, $output, $result);
  if($value === false || $result !== 0){
    trigger_error("exec value is false or result non 0 :".
                  "\ncommand : ".$command.
                  "\nvalue : ".$value.
                  "\nresult : ".$result.
                  "\noutput : ".var_export($output, true), E_USER_ERROR);
  }

  //trigger_error("output\n".print_r($output, true), E_USER_NOTICE);

  return $output;
};


function french_date_from_iso($date){
  global $date_translation;
  $date=date_create_from_format(DATE_RFC3339, $date);
  $date=strtr(date_format($date, "l j F Y à H:i:s"), $date_translation);

  //trigger_error("date\n".print_r($date, true), E_USER_NOTICE);

  return $date;
};


function post_message($message, $cookies, $cat, $topic, $post=null){
  $get_url="https://forum.hardware.fr/message.php?config=hfr.inc&cat=CAT&post=TOPIC";
  $get_url=str_replace("TOPIC", $topic, str_replace("CAT", $cat, $get_url));
  if($post !== null){
    $get_url.="&numreponse=".$post;
  }
  $context=stream_context_create(["http" => ["method" => "GET",
                                             "header" => "Cookie: ".$cookies."\r\n"]]);
  $page=file_get_contents($get_url, false, $context);
  $dom=new DOMDocument();
  @$dom->loadHTML($page);
  $xpath=new DOMXPath($dom);
  $form=$xpath->query("//form[@name=\"hop\" and @id=\"hop\"]");
  $form=$form->item(0);
  $data=[];
  $inputs1=$xpath->query(".//input[not(@type=\"checkbox\" or @type=\"radio\") ".
                         "and @name and @value]", $form);
  $inputs2=$xpath->query(".//input[(@type=\"checkbox\" or @type=\"radio\") ".
                         "and @name and @value and @checked=\"checked\"]", $form);
  foreach([$inputs1, $inputs2] as $inputs){
    foreach($inputs as $input){
      $name=$input->attributes->getNamedItem("name")->nodeValue;
      $value=$input->attributes->getNamedItem("value")->nodeValue;
      $data[$name]=$value;
    }
  }
  $selects=$xpath->query(".//select[@name]", $form);
  foreach($selects as $select){
    $name=$select->attributes->getNamedItem("name")->nodeValue;
    $options=$xpath->query(".//option[@value and @selected=\"selected\"]", $select);
    if($options->length > 0){
      $value=$options->item(0)->attributes->getNamedItem("value")->nodeValue;
      $data[$name]=$value;
    }
  }
  $data["content_form"]=$message;
  $data["MsgIcon"]="1";
  $data["signature"]="1";
  $post_url="https://forum.hardware.fr/bddpost.php?config=hfr.inc";
  if($post !== null){
    $post_url="https://forum.hardware.fr/bdd.php?config=hfr.inc";
  }
  $context=stream_context_create(["http" => ["method" => "POST",
                                             "header" => "Content-type: ".
                                             "application/x-www-form-urlencoded\r\n".
                                             "Cookie: ".$cookies."\r\n",
                                             "content" => http_build_query($data)]]);
  $result=file_get_contents($post_url, false, $context);
  $dom=new DOMDocument();
  @$dom->loadHTML($result);
  $xpath=new DOMXPath($dom);
  $div=$xpath->query("//div[@class=\"hop\"]/text()");
  $result=trim($div->item(0)->nodeValue);

  //trigger_error("result\n$result", E_USER_NOTICE);

  return $result;
};


function get_all(){
  $wiki_url="https://forum.hardware.fr/wikismilies.php?config=hfr.inc&alpha=ALPHA&page=";
  $lettres=[
    "a",
    "b",
    "c",
    "d",
    "e",
    "f",
    "g",
    "h",
    "i",
    "j",
    "k",
    "l",
    "m",
    "n",
    "o",
    "p",
    "q",
    "r",
    "s",
    "t",
    "u",
    "v",
    "w",
    "x",
    "y",
    "z",
    "|",
  ];
  $regexp_smiley='%<input type="hidden" name="smiley[0-9]+" value="\[:(.+?)\]" />%';
  $exists=[];
  $smileys=[[], []];
  foreach($lettres as $lettre){
    $lettre_url=str_replace("ALPHA", $lettre, $wiki_url);
    $page_number=1;
    while($page_number === 1 || count($matches[1]) > 0){
      $page_url=$lettre_url.$page_number++;
      $page=file_get_contents($page_url);
      preg_match_all($regexp_smiley, $page, $matches);
      foreach($matches[1] as $smiley){
        $smiley=html_entity_decode($smiley, ENT_QUOTES | ENT_SUBSTITUTE | ENT_XML1,
                                   "UTF-8");
        if(!in_array($smiley, $exists, true)){
          $exists[]=$smiley;
          $split=explode(":", $smiley, 2);
          $smileys[0][]=$split[0];
          $smileys[1][]=count($split) === 2 ? (int)$split[1] : 0;
        }
      }
    }
  }
  $list="";
  setlocale(LC_ALL, "fr_FR");
  array_multisort($smileys[0], SORT_ASC, SORT_LOCALE_STRING | SORT_FLAG_CASE,
                  $smileys[1], SORT_ASC, SORT_NUMERIC);
  foreach($smileys[0] as $i => $smiley){
    $list.=$smiley.($smileys[1][$i] !== 0 ? ":".$smileys[1][$i] : "")."\n";
  }
  file_put_contents("../generateurs/_api/smileys.txt", trim($list));
};


