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

userIsConnected('ROLE_ADMIN');


/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'listeComment.phtml';      //vue qui sera affichée dans le layout
$title = 'Tous les commentaires';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'listeComment';   //menu qui sera sélect dans la nav du layout

try
{
    $bdd = connexion();
    $sth = $bdd->prepare('SELECT * 
    FROM '.DB_PREFIXE.'comment
    INNER JOIN '.DB_PREFIXE.'article ON c_article=a_id
    ORDER BY c_valide ASC');
    $sth->execute();

    $flashbag = getFlashBag();
   
    $comments = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
