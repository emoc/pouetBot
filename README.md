# pouetBot

Bot IRC qui transcrit les conversations en MIDI. Chaque participant·e du canal IRC est associé à un canal MIDI et un instrument différent.

Les caractères alphanumériques déclenchent des notes, les caractères de ponctuation déclenchent un hit de percussion selon des tables de correspondances préétablies.

Les chaines de caractères sont traités pour que chaque note soit envoyée sur un tempo défini

Quelques commandes permettent aux participant·e·s d'interagir :

* !change : changement d'instrument au hasard
* !instrument [NUMERO] : changement d'instrument, NUMERO est compris entre 1 et 128, selon le standard General MIDI 1
* !panique : coupe toutes les notes (envoie ALL NOTES OFF et ALL SOUNDS OFF sur les 16 canaux MIDI)

## Fonctionnement

![schéma de fonctionnement](https://github.com/emoc/pouetBot/blob/master/mibot_schema_fonctionnement.png)

## Documentation

Documentation détaillée : http://lesporteslogiques.net/wiki/openatelier/projet/bot_irc_midi

  