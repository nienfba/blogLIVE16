<?php

/**On inclu d'abord le fichier de configuration */
include('config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('lib/app.lib.php');

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'index.phtml';      //vue qui sera affichée dans le layout
$title = 'Accueil';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = '';   //menu qui sera sélect dans la nav du layout

try
{
    $bdd = connexion();
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article INNER JOIN '.DB_PREFIXE.'user ON a_author=u_id LEFT JOIN '.DB_PREFIXE.'categorie ON a_categorie=c_id');
    $sth->execute();
   
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
