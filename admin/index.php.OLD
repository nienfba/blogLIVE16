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

try {
    $bdd = connexion();

    /** On va récupérer les catégories dans la bdd*/
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

    //On ordonne les catégories par ordre hirarchique avec un niveau (ATTENTION niveau avancé...)
    $categories = orderCategoriesLevel($categories);

    $bdd = connexion();
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article INNER JOIN '.DB_PREFIXE.'user ON a_author=u_id LEFT JOIN '.DB_PREFIXE.'categorie ON a_categorie=c_id ORDER BY a_date_created DESC LIMIT 1,5 ');
    $sth->execute();
   
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);


}
catch(PDOException $e)
{
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $errorForm[] = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');