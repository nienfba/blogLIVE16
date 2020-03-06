<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'category/liste';      //vue qui sera affichée dans le layout
$title = 'Toutes les catégories';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'listeCategory';   //menu qui sera sélect dans la nav du layout

try
{
    $bdd = connexion();
    $sth = $bdd->prepare('SELECT c1.cat_id, c1.cat_title, c2.cat_title as parent, c1.cat_parent, COUNT(a.art_id) as articles  
                        FROM '.DB_PREFIXE.'categorie c1 
                        LEFT JOIN '.DB_PREFIXE.'categorie c2 ON c1.cat_parent=c2.cat_id 
                        LEFT JOIN '.DB_PREFIXE.'article a ON c1.cat_id = a.art_categorie 
                        GROUP BY c1.cat_id,c2.cat_id 
                        ORDER BY c1.cat_title, c1.cat_parent');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

    /**  On va créer un tableau des catégorie hiérarchisée pour afficher des ul>li hiérarchiques (arbre des catégories parent/enfants)
    * Utilisation d'une fonction récursive (pour l'exemple algorithmique !).
    */
    $orderedCategories = orderCategories($categories);

    $flashbag = getFlashBag();

}
catch(PDOException $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
