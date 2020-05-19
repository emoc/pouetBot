#!/usr/bin/php -q 
<?php 

/*
  Script pour tester l'envoi de smessages UDP à pure data
  PHP 7.0.33-0+deb9u7 (cli) / Pd 0.47.1 @ Kirin, Debian strech 9.5
  Quimper, Dour Ru, 19 mai 2020 / pierre@lesporteslogiques.net
*/

// Créer un socket sur le port UDP 5113 de la machine locale
$fp = stream_socket_client("udp://127.0.0.1:5113", $errno, $errstr);

if (!$fp) {
  echo "ERREUR : $errno - $errstr<br />\n";
} else {
  while (1) {
    $message = "note 1 64 100 500;\n";
    echo "$message";  
    fwrite($fp, $message);   // Envoyer le message sur le socket
    $message = "inst 1 64;\n";
    echo "$message";  
    fwrite($fp, $message);   // Envoyer le message sur le socket
    sleep(1);
  }
  fclose($fp);
  exit();
}
