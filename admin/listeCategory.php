<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');
include('../lib/bdd.lib.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'listeCategory.phtml';      //vue qui sera affichée dans le layout
$title = 'Toutes les catégories';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'listeCategory';   //menu qui sera sélect dans la nav du layout

try
{

    $categories = listParentOrderedCategory();

    /**  On va créer un tableau des catégorie hiérarchisée pour afficher des ul>li hiérarchiques (arbre des catégories parent/enfants)
    * Utilisation d'une fonction récursive (pour l'exemple algorithmique !).
    */
    $orderedCategories = orderCategories($categories);

    $flashbag = getFlashBag();

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
