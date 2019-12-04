<?php
session_start();
/** On veut lister les articles. On doit soit :
 * 1. Récupérer tous les articles en bdd
 * 2. Afficher les articles ! 
 */

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');
include('../lib/bdd.lib.php');
include('../lib/models/Article.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'listeArticle.phtml';      //vue qui sera affichée dans le layout
$title = 'Tous les articles';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'listeArticle';   //menu qui sera sélect dans la nav du layout

try
{
    $flashbag = getFlashBag();

    $modelArticle = new Article();
    $articles = $modelArticle->list();
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
