<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');


/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'listeCategory.phtml';      //vue qui sera affichée dans le layout
$title = 'Toutes les catégories';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'listeCategory';   //menu qui sera sélect dans la nav du layout

try
{
    $bdd = connexion();
    $sth = $bdd->prepare('SELECT c1.c_id, c1.c_title, c2.c_title as parent, COUNT(a.a_id) as articles  FROM '.DB_PREFIXE.'categorie c1 LEFT JOIN '.DB_PREFIXE.'categorie c2 ON c1.c_parent=c2.c_id LEFT JOIN '.DB_PREFIXE.'article a ON c1.c_id = a.a_categorie GROUP BY c1.c_id ORDER BY c1.c_parent');
    $sth->execute();


    $flashbag = getFlashBag();
   
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
