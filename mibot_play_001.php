#!/usr/bin/php -q 
<?php 

/*

  Interpréter des chaines de caractères pour en extraire des données MIDI à transmettre à pure data
  reçoit : une chaine de caractères
  PHP 7.0.33-0+deb9u7 (cli) / Pd 0.47.1 @ Kirin, Debian strech 9.5
  Argenton, 16 mai 2020 / pierre@lesporteslogiques.net
  
  Syntaxe
    mibot_play_001.php --chan=CANAL --tempo=VALEUR --util="USER" --seq="MESSAGE"
    
    CANAL    : entre 1 et 16  (canal MIDI)
    VALEUR   : un nombres entier (en microsecondes)
    USER     : nom de l'utilisateur (inutile)
    MESSAGE  : chaine de caractères à traiter

  Messages envoyés
    note {channel} {note} {vel} {dur};    : jouer la {note} sur ce {channel} MIDI avec une vélocité {vel} et une durée {dur}
    inst {channel} {inst};                : changer l'instrument du {channel} par {inst}
    perc {channel} {inst} {vel} {dur};    : jouer une percussion sur le {channel} ...
    
    channel : entre 1 et 16  (canal MIDI)
    note    : entre 0 et 127 (note à jouer)
    vel     : entre 0 et 127 (vélocité)
    dur     : entier (durée en millisecondes)
    
    TODO : user ne sert à rien
*/

$notes = [
 21,  23,  24,  26,  28,  29,  31, 
 33,  35,  36,  38,  40,  41,  43,
 45,  47,  48,  50,  52,  53,  55,
 57,  59,  60,  62,  64,  65,  67,
 69,  71,  72,  74,  76,  77,  79,
 81,  83,  84,  86,  88,  89,  91,
 93,  95,  96,  98, 100, 101, 103,
105, 107, 108, 110, 112, 113, 115,
117, 119, 120, 122, 124, 125 ];

$alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

$nombres = "0123456789";

$ponct = ",;-!*?:/&(_)+=.#%éèçà$@]äâ}{îï|ùêüôöÔÛÎÖïÜ";

$percs = [
35, 36, 37, 38, 39, 40, 41, 
42, 43, 44, 45, 46, 47, 48, 
49, 50, 51, 52, 53, 54, 55, 
56, 57, 58, 59, 60, 61, 62, 
63, 64, 65, 67, 68, 69, 70, 
71, 72, 73, 74, 75, 76, 77]; 

$arguments = arguments($argv);

foreach ($arguments as $action => $valeur) {
  if ($action == "util") {
    $util = $valeur;
  }
  if ($action == "seq") {
    $seq = $valeur;
  }
  if ($action == "chan") {
    $chan = $valeur;
  }
  if ($action == "tempo") {
    $tempo = $valeur;
  }
}

// Créer un socket sur le port UDP 5113 de la machine locale
$fp = stream_socket_client("udp://127.0.0.1:5113", $errno, $errstr);

$chaine = str_split(rawurldecode($seq));

if (!$fp) {

    echo "ERREUR : $errno - $errstr<br />\n";
    
} else {

  $r = strpos($nombres, $chaine[0]);
  $repet = 1;
  if ($r !== false) {
    $repet = $r;
  }
  if ($repet == 0) $repet = 1;
    
  for ($i = 0; $i < $repet; $i++) {
    $compteur = 0; // Utilisé pour sauter le premier caractère...
    foreach($chaine as $car) {
    
      $ok = false;
      $message = "";
      $note = "";
      $inst = "";
      
      // Commencer par tester si $car est un caratère alphanumérique
      $pos = strpos($alpha, $car);
      if ($pos !== false) {
        $note = $notes[$pos];
        $duree = (100 - count($chaine)) * 10;
        if ($duree < 10) $duree = 10;
        $message = "note " . $chan . " " . $note . " " . 100 . " " . $duree . ";\n";
        $ok = true;
      } 
      
      // Sinon, tester si cela correspond à une percussion
      $pos = mb_strpos($ponct, $car);
      if ($pos !== false) {
        if ($pos > (count($percs) - 1)) $pos = count($percs) - 1; // cracra TODO mieux
        $inst = $percs[$pos];
        $duree = (100 - count($chaine)) * 10;
        if ($duree < 10) $duree = 10;
        $message = "perc 10 " . $inst . " " . 127 . " " . $duree . ";\n";
        $ok = true;
      }
      
      if ( ($compteur == 0) && ($repet > 1) ) $ok = false;
      
      // Dans tous les autres cas, on n'envoie rien mais on laisse un silence
      if ($ok) {
        echo "$util : $message";  
        fwrite($fp, $message);   // Envoyer le message sur le socket
      }
      
      unset($pos);
      usleep($tempo);
      $compteur ++;
    }
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
