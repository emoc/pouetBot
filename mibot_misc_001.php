#!/usr/bin/php -q 
<?php 

/*
  Envoi de message UDP à pure data (qui les transformera en MIDI)
  PHP 7.0.33-0+deb9u7 (cli) / Pd 0.47.1 @ Kirin, Debian strech 9.5
  Quimper, Dour Ru, 17 mai 2020 / pierre@lesporteslogiques.net

  Syntaxe :
    mibot_misc_001.php --do=ACTION [--chan=[CANAL]]
    
    ACTION : 
      panic (en MIDI enverra un message ALL NOTES OFF)
    CANAL  : entre 1 et 16  (canal MIDI)
    
  Message envoyé par UDP à pure data :
    panic;
*/

$arguments = arguments($argv);

foreach ($arguments as $action => $valeur) {
  if ($action == "chan") {
    $chan = $valeur;
  }
  if ($action == "do") {
    $do = $valeur;
  }
}

// Créer un socket sur le port UDP 5113 de la machine locale
$fp = stream_socket_client("udp://127.0.0.1:5113", $errno, $errstr);

if (!$fp) {
  echo "ERREUR : $errno - $errstr<br />\n";
} else {
  if ($do == 'panic') {
    $message = "panic;\n";
    echo "$message";  
    fwrite($fp, $message);   // Envoyer le message sur le socket
  }
  fclose($fp);
  exit();
}

/*
  Fonction pour récupérer des arguments au lancement du script
  sous la forme : php myscript.php --user=nobody --password=secret -p --access="host=127.0.0.1 port=456" 
  d'après https://www.php.net/manual/fr/features.commandline.php#78093
*/
function arguments($argv) {
  $_ARG = array();
  foreach ($argv as $arg) {
    if (preg_match('#^-{1,2}([a-zA-Z0-9]*)=?(.*)$#', $arg, $matches)) {
      $key = $matches[1];
      switch ($matches[2]) {
        case '':
        case 'true':
          $arg = true;
          break;
        case 'false':
          $arg = false;
          break;
        default:
          $arg = $matches[2];
      }
      $_ARG[$key] = $arg;
    }
    else {
      $_ARG['input'][] = $arg;
    }
  }
  return $_ARG;
}
?>
