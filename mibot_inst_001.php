#!/usr/bin/php -q 
<?php 

/*
  Envoyer un message pour changer d'instrument
  PHP 7.0.33-0+deb9u7 (cli) / Pd 0.47.1 @ Kirin, Debian strech 9.5
  Argenton, 16 mai 2020 / pierre@lesporteslogiques.net
  
  Syntaxe
    mibot_inst_001.php --chan=CANAL --inst=INSTRUMENT
    
    CANAL      : entre 1 et 16  (canal MIDI)
    INSTRUMENT : entre 1 et 128 (instrument à assigner)
 
  Message envoyé par UDP à pure data
    inst {channel} {inst};     : changer l'instrument du {channel} par {inst}
     
    channel : entre 1 et 16  (canal MIDI)
    inst : entre 1 et 128 (instrument à assigner)
*/


$arguments = arguments($argv);

foreach ($arguments as $action => $valeur) {
  if ($action == "chan") {
    $chan = $valeur;
  }
  if ($action == "inst") {
    $inst = $valeur;
  }
}

// Créer un socket sur le port UDP 5113 de la machine locale
$fp = stream_socket_client("udp://127.0.0.1:5113", $errno, $errstr);

if (!$fp) {

  echo "ERREUR : $errno - $errstr<br />\n";
    
} else {
  $message = "inst " . $chan . " " . $inst . ";\n";
  echo "canal $chan : instrument $message";  
  fwrite($fp, $message);   // Envoyer le message sur le socket
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
