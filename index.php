<?php

/**On inclu d'abord le fichier de configuration */
include('config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('lib/app.lib.php');

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'index';      //vue qui sera affichée dans le layout
$title = 'lEARN & pLAY';  //titre de la page qui sera mis dans title et h1 dans le layout
$subTitle = 'dU gAME eT dU cODE'; //sous titre
$menuSelected = '';   //menu qui sera sélect dans la nav du layout
$bgImage = 'img/home-bg.jpg';
try
{
    $bdd = connexion();
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article INNER JOIN '.DB_PREFIXE.'user ON a_author=u_id LEFT JOIN '.DB_PREFIXE.'categorie ON a_categorie=c_id WHERE a_valide=1');
    $sth->execute();
   
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
