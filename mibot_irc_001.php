#!/usr/bin/php -q 
<?php

/*
  Ecoute d'un canal IRC pour le transformer en MIDI
    - déclenchement de scripts d'action pour l'envoi de notes, le changement d'instruments, etc.
    - gesetion de l'attribution des canaux MIDI aux participant·e·s du canal IRC
  PHP 7.0.33-0+deb9u7 (cli) / Pd 0.47.1 @ Kirin, Debian strech 9.5
  Quimper, Dour Ru, 17 mai 2020 / pierre@lesporteslogiques.net

  Syntaxe :
  
    php mibot_irc_001.php
        
*/



$msg_instruments = [
"joue maintenant du piano acoustique de concert",
"joue maintenant du piano acoustique",
"joue maintenant du grand piano électrique",
"joue maintenant du piano honky-tonk",
"joue maintenant du piano électrique",
"joue maintenant de l'autre piano électrique",
"joue maintenant du clavecin",
"joue maintenant du clavicorde",
"joue maintenant des percussions chromatiques",
"joue maintenant du célesta",
"joue maintenant du carillon",
"joue maintenant de la boîte à musique",
"joue maintenant du vibraphone",
"joue maintenant du marimba",
"joue maintenant du xylophone",
"joue maintenant des cloches tubulaires",
"joue maintenant du tympanon",
"joue maintenant de l'orgue à tubes",
"joue maintenant de l'orgue percussif",
"joue maintenant de l'orgue rock",
"joue maintenant de l'orgue d'Eglise",
"joue maintenant de l'orgue vibrato",
"joue maintenant de l'accordéon",
"joue maintenant de l'harmonica",
"joue maintenant de l'accordéon tango",
"joue maintenant de la guitare acoustique nylon",
"joue maintenant de la guitare acoustique acier",
"joue maintenant de la guitare électrique jazz",
"joue maintenant de la guitare électrique pure",
"joue maintenant de la guitare électrique sourdine",
"joue maintenant de la guitare électrique saturée",
"joue maintenant de la guitare électrique avec distorsion",
"joue maintenant de la guitare électrique - harmoniques",
"joue maintenant de la basse acoustique",
"joue maintenant de la basse électrique",
"joue maintenant de l'autre basse électrique",
"joue maintenant de la troisième basse électrique",
"joue maintenant de la bass slap",
"joue maintenant de l'autre bass slap",
"joue maintenant de la basse synthétiseur",
"joue maintenant de l'autre basse synthétiseur",
"joue maintenant du violon",
"joue maintenant du violon alto",
"joue maintenant du violoncelle",
"joue maintenant de la contrebasse",
"joue maintenant des cordes (trémolo)",
"joue maintenant des cordes (pizzicato)",
"joue maintenant de la harpe",
"joue maintenant des timbales",
"joue maintenant dans un quatuor à cordes",
"joue maintenant dans l'autre quatuor à cordes",
"joue maintenant des cordes synthétiques",
"joue maintenant des autres cordes synthétiques",
"chante maintenant dans les choeurs Aahs",
"chante maintenant dans les choeurs Oohs",
"chante maintenant d'une voix synthétique",
"joue maintenant dans un orchestre",
"joue maintenant de la trompette",
"joue maintenant du trombone",
"joue maintenant du tuba",
"joue maintenant de la trompette bouchée",
"joue maintenant des cors",
"joue maintenant dans un ensemble de cuivres",
"joue maintenant des cuivres synthétiques",
"joue maintenant des autres cuivres synthétiques",
"joue maintenant du saxophone soprano",
"joue maintenant du saxophone alto",
"joue maintenant du saxophone ténor",
"joue maintenant du saxophone baryton",
"joue maintenant du hautbois",
"joue maintenant du cor anglais",
"joue maintenant du basson",
"joue maintenant de la clarinette",
"joue maintenant du piccolo",
"joue maintenant de la flûte",
"joue maintenant de la flûte à bec",
"joue maintenant de la flûte de Pan",
"joue maintenant de la bouteille sifflée",
"joue maintenant du shakuhachi",
"joue maintenant du sifflet",
"joue maintenant de l'ocarina",
"surfe maintenant sur l'onde carrée",
"surfe maintenant sur l'onde dent de scie",
"joue maintenant de l'orgue à vapeur",
"joue maintenant du chiff",
"joue maintenant du charang",
"chante maintenant",
"joue maintenant de la quinte",
"joue maintenant en solo et basse",
"se détend dans une ambiance new age",
"ambiance chaude",
"joue maintenant du synthétiseur polyphonique",
"chante maintenant dans les choeurs",
"joue maintenant avec son archet",
"joue maintenant des sons métalliques",
"s'entoure d'un halo",
"s'essaie au balayage",
"déclenche une pluie de glace",
"rêve dans une bande sonore",
"vide son verre en cristal",
"s'élève dans l'atmosphère",
"gagne en brillance",
"a réveillé les goblins",
"excite les échos",
"nage en pleine science-fiction",
"joue maintenant de la sitar",
"joue maintenant du banjo",
"joue maintenant du shamisen",
"joue maintenant du koto",
"joue maintenant du kalimba",
"joue maintenant de la cornemuse",
"joue maintenant du violon",
"joue maintenant du shanai",
"appuie sur la sonnerie",
"gratte son agogo",
"s'énerve sur ses percussions en acier",
"tape sur son woodblock",
"tape sur son tambour Taiko",
"s'emballe sur son tom mélodique",
"joue maintenant des percussions synthétiques",
"transfigure sa cymbale inversée",
"gratouille ses frettes de guitare",
"prend une grande respiration",
"surfe la vague",
"s'égosille sur sa branche",
"attend un appel important",
"s'évade en hélicoptère",
"applaudit",
"tire dans le tas"
];

/*
  Structure de donnée, tableau de tableau
  chaque sous-tableau contient les données suivantes
  index 0 : attribué ou pas (0 ou 1)
  index 1 : nom d'utilisateur
  index 2 : canal MIDI (en commençant à zéro)
  index 3 : dernière interaction (utile pour réattribuer un canal)
*/
$participation = [
  [0, "",  1, 0],
  [0, "",  2, 0],
  [0, "",  3, 0],
  [0, "",  4, 0],
  [0, "",  5, 0],
  [0, "",  6, 0],
  [0, "",  7, 0],
  [0, "",  8, 0],
  [0, "",  9, 0],
  [0, "", 11, 0],
  [0, "", 12, 0],
  [0, "", 13, 0],
  [0, "", 14, 0],
  [0, "", 15, 0],
  [0, "", 16, 0]
];


$MIDIOK = false;

// Variables pour IRC
$channels  = array('#labaleine');
$nickname = 'pouetBot';
// $password = 'secret';
$master   = 'emoc';

// Avertissement sonore
$cmd = "espeak -v fr+f4 -x -s160 -p50 \"Connexion de pouetBot. Attention niveau sonore inconnu. Cinq. Quatre. Trois. Deux. Un. \"";
echo $cmd . PHP_EOL;
echo exec($cmd) . PHP_EOL;
              
// Ouvrir le socket
$socket = fsockopen("irc.freenode.net", 6667);

// Authentification
// fputs($socket, "PASS " . $password . "\n");
fputs($socket, "NICK " . $nickname . "\n");
fputs($socket, "USER " . $nickname . " 0 * :" . $master . "'s Bot\n");

// Rejoindre les canaux
foreach($channels as $channel) {
	fputs($socket, "JOIN " . $channel . "\n");
}

// Créer un socket sur le port UDP 5113 de la machine locale
$fp = stream_socket_client("udp://127.0.0.1:5113", $errno, $errstr);

// Un premier timestamp pour éviter l'avalanche de notes des messages freenode...
$date0 = date_create();
$timestamp0 = date_timestamp_get($date0);

// Boucle sans fin, le script reste en écoute
while (1) {
  
    while ($data = fgets($socket, 128)) {
    
        $date = date_create();
        $timestamp = date_timestamp_get($date);
        
        // On déclenche les actions MIDI après quelques secondes
        if ($timestamp - $timestamp0 > 5) $MIDIOK = true;
  
        echo $data;  // Données reçues
        flush();
        
        // Découper le message reçu
        $ex = explode(' ', $data);
        
        $parts = explode("!", $ex[0]);
        $user  = substr($parts['0'], 1);
        // $ex[2] : nom du salon
        // $ex[3] : nom de la commande (s'il y en a un)
        
        echo "reçu message de $user ";
        
        // Gestion de l'attribution des canaux *********
        $libre = -1;
        $olduser = false;
        
        // Est ce que cet utilisateur est connu ?
        foreach ($participation as $k => $val) {
          if ($val[0] == 0) $libre = $k;
          if ($val[1] == $user) {
            $olduser = true;
            $val[3] = $timestamp;
            $chan = $val[2];
          }
        }
        if (!$olduser) { // Nouvel utilisateur
        
          if ($libre >= 0) { // il y a un canal libre
          
            $participation[$libre][0] = 1;
            $participation[$libre][1] = $user;
            $chan = $participation[$libre][2];
            $participation[$libre][3] = $timestamp;
            
          } else { // Il n'y en a pas, alors chaises musicales...
          
            //$tsolder = 9589810391;  // un vieux timestamp : 21 nov 2273 (bug à prévoir dans 253 ans)
            $ecartmax = 0;
            $older = -1;
            foreach ($participation as $k => $val) {
              if (($timestamp - $participation[$k][3]) > $ecartmax) {
                $ecartmax = $timestamp - $participation[$k][3];
                $older = $k;
              }
            }
            $ecart = $timestamp - $participation[$older][3];
            $exuser = $participation[$older][1];
            $chan = $participation[$older][2];
            $participation[$older][0] = 1;
            $participation[$older][1] = $user;
            $participation[$older][3] = $timestamp;
            fputs($socket, "PRIVMSG " . $ex[2] . " :    Sorry! " . $user . " remplace " . $exuser . " sur le canal " . $chan . " \n");
          }
        }
        
        if ($ex[0] == "PING") {  // Send PONG back to the server
        
            fputs($socket, "PONG " . $ex[1] . "\n");
            
        }
        
        if ($ex[0] != 'PING' && ISSET($ex[3])) { // Traiter le message reçu
            $command = str_replace(array(
                chr(10),
                chr(13)
            ), '', $ex[3]);

            if ($command == ":!panique") {      // Stopper toutes les notes existantes
              
              $cmd = "php ./mibot_misc_001.php --do=panic > /dev/null 2>/dev/null &";
              if ($MIDIOK) echo $cmd . PHP_EOL;
              if ($MIDIOK) echo exec($cmd) . PHP_EOL;
                
            } else if ($command == ":!change") { // l'utilisateur veut changer d'instrument au hasard!
              
              $newinst = rand(1, 128);
              $cmd = "php ./mibot_inst_001.php --chan=" . $chan . " --inst=".$newinst . " > /dev/null 2>/dev/null &";
              if ($MIDIOK) echo $cmd . PHP_EOL;
              if ($MIDIOK) echo exec($cmd) . PHP_EOL;
              if ($MIDIOK) fputs($socket, "PRIVMSG " . $ex[2] . " :    " . $user . " " . $msg_instruments[$newinst] . " \n");
              if ($MIDIOK) echo "$user " . $msg_instruments[$newinst] . PHP_EOL;
              
            } else if ($command == ":!instrument") { // l'utilisateur veut changer d'instrument!
            
              if (count($ex) >= 4) {
                $newinst = $ex[4] + 0;
                if ( (1 <= $newinst) && ($newinst <= 128) ) {
                  $cmd = "php ./mibot_inst_001.php --chan=" . $chan . " --inst=".$newinst . " > /dev/null 2>/dev/null &";
                  if ($MIDIOK) echo $cmd . PHP_EOL;
                  if ($MIDIOK) echo exec($cmd) . PHP_EOL;
                  if ($MIDIOK) fputs($socket, "PRIVMSG " . $ex[2] . " :    " . $user . " " . $msg_instruments[$newinst] . " \n");
                  if ($MIDIOK) echo "$user " . $msg_instruments[$newinst] . PHP_EOL;
                }
              }
            
            } else if ($command == ":!zap") { // ADMIN l'admin libère un slot de participation

              if ($user == $master) {
                if (count($ex) >= 4) {
                  $slot = $ex[4] + 0;
                  if ( (0 <= $slot) && ($slot <= 14) ) {
                    $participation[$slot][0] = 0;
                    $participation[$slot][1] = "";
                    $participation[$slot][3] = "";
                  }
                }
              }
              
            } else if ($command == ":!help") {
            
              man();
              
            } else if ($command == ":!aide") {
            
              man();
              
            } else if ($command == ":!man") {
              
              man();
              
            } else if ($command == ":!verif") { // DEBUG Vérification du tableau des participations
            
              print_r($participation);
              
            } else if ($command == ":!test") { // DEBUG Renvoi des valeurs sur la canal
            
              fputs($socket, "PRIVMSG " . $ex[2] . " :value0 " . $ex[0] . ", value1 " . $ex[1] . ",value2 " . $ex[2] . ",value3 " . $ex[3] . "\n");
              
            }  else { // Ca joue!
            
              // Préparer la séquence de caractères à envoyer
              $seq = "";
              
              if (count($ex) >= 4) {
                for ($i = 3; $i < count($ex); $i++) {
                  if ($i > 3) $seq .= " ";
                  $seq .= $ex[$i];
                }
              }

              $seq = substr($seq, 1);
              $seq = rawurlencode($seq);
              $tempo = 125000;

              if ($MIDIOK) $cmd = "php ./mibot_play_001.php --chan=" . $chan . " --tempo=".$tempo." --util=\"" . $user . "\" --seq=\"" .  $seq . "\" > /dev/null 2>/dev/null &";
              if ($MIDIOK) echo $cmd . PHP_EOL;
              if ($MIDIOK) echo exec($cmd) . PHP_EOL;
              
            }
        }
    }
}

function man() {
  fputs($socket, "PRIVMSG " . $ex[2] . " :Salut c'est pouetBot. Je vous écoute. Demandez-moi : !change (changement d'instrument au hasard) !instrument [NUMERO] (changement d'instrument, NUMERO est compris entre 1 et 128, selon le standard General MIDI) !panique (coupe toutes les notes) ; Commencer une ligne par un chiffre (entre 1 et 9) définit le nombre de répétitions de la ligne ;  \n");
  usleep(500000);
  fputs($socket, "PRIVMSG " . $ex[2] . " :tous les caractères alphanumériques définissent une note ; tous les caractères accentués et de ponctuation définissent un hit d'instrument de percussion. Plus de détails ? http://lesporteslogiques.net/wiki/openatelier/projet/bot_irc_midi \n");
}
?>
