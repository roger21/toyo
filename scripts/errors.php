<?php


use moi\colors as c;


ini_set("error_reporting", "-1");

define("ERROR_DATE_FORMAT", "[Y-m-d H:i:s]");
define("ERROR_INDENT", "                      ");
define("ERROR_START_MESSAGE", "");
define("ERROR_END_MESSAGE", "\n\n\n");
define("ERROR_DIE_MESSAGE", "\n".ERROR_INDENT.c\red("SCRIPT TERMINATION"));

$errors=[
  0     => ["name" => c\cyan("E_@"),                 "die" => false],
  1     => ["name" => c\red("E_ERROR"),              "die" => true ],
  2     => ["name" => c\yellow("E_WARNING"),         "die" => false],
  4     => ["name" => c\alert("E_PARSE"),            "die" => true ],
  8     => ["name" => c\green("E_NOTICE"),           "die" => false],
  16    => ["name" => c\alert("E_CORE_ERROR"),       "die" => true ],
  32    => ["name" => c\alert("E_CORE_WARNING"),     "die" => true ],
  64    => ["name" => c\alert("E_COMPILE_ERROR"),    "die" => true ],
  128   => ["name" => c\alert("E_COMPILE_WARNING"),  "die" => true ],
  256   => ["name" => c\red("E_USER_ERROR"),         "die" => true ],
  512   => ["name" => c\yellow("E_USER_WARNING"),    "die" => false],
  1024  => ["name" => c\green("E_USER_NOTICE"),      "die" => false],
  2048  => ["name" => c\cyan("E_STRICT"),            "die" => false],
  4096  => ["name" => c\cyan("E_RECOVERABLE_ERROR"), "die" => false],
  8192  => ["name" => c\cyan("E_DEPRECATED"),        "die" => false],
  16384 => ["name" => c\cyan("E_USER_DEPRECATED"),   "die" => false],
];

function log_error($message){
  global $NO_LOGGING_ERROR, $ECHOING_ERROR;
  $message=ERROR_START_MESSAGE.date(ERROR_DATE_FORMAT)." ".$message.ERROR_END_MESSAGE;
  echo $message;
};

function error_handler($errno, $errstr, $errfile, $errline){
  if((strpos($errstr, "DOMDocument::loadHTML") === 0) &&
     (($errno === E_WARNING) || ($errno === E_NOTICE))){
    return;
  }
  $hidden_error=(error_reporting() !== -1);
  global $errors;
  $message="";
  if($hidden_error){
    $message.=$errors[0]["name"]." ";
  }
  $errstr=str_replace("\t", "    ", str_replace("\n", "\n".ERROR_INDENT, trim($errstr)));
  $message.=$errors[$errno]["name"]." ".
          c\grey(substr(strrchr($errfile, '/'), 1)." : ".$errline)."\n".
          ERROR_INDENT.$errstr;
  if($errors[$errno]["die"] || ($hidden_error && $errors[0]["die"])){
    $message.=ERROR_DIE_MESSAGE;
  }
  log_error($message);
  if($errors[$errno]["die"] || ($hidden_error && $errors[0]["die"])){
    exit();
  }
};
set_error_handler("error_handler");


