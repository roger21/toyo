<?php


namespace moi\colors{


  function grey($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[90m".$string."\033[0m");
    } else {
      return("\033[47;97m".$string."\033[0m");
    }
  }

  function red($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[91m".$string."\033[0m");
    } else {
      return("\033[101;97m".$string."\033[0m");
    }
  }

  function green($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[92m".$string."\033[0m");
    } else {
      return("\033[102;30m".$string."\033[0m");
    }
  }

  function yellow($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[93m".$string."\033[0m");
    } else {
      return("\033[103;30m".$string."\033[0m");
    }
  }

  function blue($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[94m".$string."\033[0m");
    } else {
      return("\033[104;97m".$string."\033[0m");
    }
  }

  function purple($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[95m".$string."\033[0m");
    } else {
      return("\033[105;97m".$string."\033[0m");
    }
  }

  function cyan($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[96m".$string."\033[0m");
    } else {
      return("\033[106;30m".$string."\033[0m");
    }
  }

  function alert($string){
    global $LIGHT_COLOR, $DARK_BACKGROUND;
    if((isset($LIGHT_COLOR) && ($LIGHT_COLOR === true)) ||
       (isset($DARK_BACKGROUND) && ($DARK_BACKGROUND === true))){
      return("\033[5;41;93m".$string."\033[0m");
    } else {
      return("\033[5;41;93m".$string."\033[0m");
    }
  }


}


