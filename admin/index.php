<?php
session_start();


/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');


userIsConnected();


/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'index';   //vue qui sera affichée dans le layout
$title =  'Accueil';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'home';       //menu qui sera sélect dans la nav du layout

try {
    $bdd = connexion();

    /** On va récupérer les catégories dans la bdd*/
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

    //On ordonne les catégories par ordre hiérarchique (ATTENTION niveau avancé...)
    $categories = orderCategoriesLevel($categories);

    
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article 
                        INNER JOIN '.DB_PREFIXE.'user ON art_author=use_id 
                        LEFT JOIN '.DB_PREFIXE.'categorie ON art_categorie=cat_id 
                        ORDER BY art_date_created DESC 
                        LIMIT 5 ');
    $sth->execute();
    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);

    /** Récupération des derniers commentaires */
    $sth = $bdd->prepare('SELECT c.*,a.art_title FROM ' . DB_PREFIXE . 'comment c
                        INNER JOIN '.DB_PREFIXE . 'article a ON (c.com_article=a.art_id)
                        ORDER BY c.com_valide ASC, c.com_date_posted DESC
                        LIMIT 5');
    $sth->execute();
    $comments = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');