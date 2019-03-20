<?php
session_start();


/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');


userIsConnected();


/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'index.phtml';   //vue qui sera affichée dans le layout
$title =  'Accueil';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'home';       //menu qui sera sélect dans la nav du layout


include('tpl/layout.phtml');