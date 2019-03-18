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


/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'listeArticle.phtml';      //vue qui sera affichée dans le layout
$title = 'Tous les articles';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'listeArticle';   //menu qui sera sélect dans la nav du layout

try
{
    $bdd = connexion();
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article INNER JOIN '.DB_PREFIXE.'user ON a_author=u_id LEFT JOIN '.DB_PREFIXE.'categorie ON a_categorie=c_id');
    $sth->execute();


    $flashbag = getFlashBag();
   
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
