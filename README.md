scoobydoo
=========

C'est le client d'administration de payutc...

Pour ajouter un module minimaliste il faut :
============================================
créer un dossier dans /modules portant le nom du module en minuscule et réaliser l'architecture suivante

* action (contiendra toutes les actions du module)

** [nomdemonaction].action.php (les actions sont éxécutés depuis la classe Module. on a donc $this->view pour intérragir sur la view (changer la vue ou le template par exemple) et passer les parametres par $this->view->set_param($key, $monparam))

* view (contiendra toutes les vues du module)

** [nomdemaview].view.phtml (toutes les vues, elle seront éxécutés par la classe view on peut donc y faire des $this->get_param($key) (pour récupérer les paramétres passé à la vue))

* Module[Nomdemonmdule].class.php

WARNING
=======
Pour un usage en production il est vitale de ne laisser que index.php comme point d'entrée...
Un accès direct sur les actions pourrait avoir un comportement innatendu...
En théorie, rien de dramatique car les interfaces SOAP n'étant pas logué, elle devrait refuser d'intérragir coté serveur... 