<?php
session_start();
/** On veut modifier juste la publication de l'article
 * On pourrait même appeler cette page en AJAX mais pour le moment on le faire avec une requête HTTP classique 
 */
/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');

userIsConnected('ROLE_ADMIN');

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = '';      //vue qui sera affichée dans le layout - m^me vue que l'ajout
$title = 'Publier/Dépublier un commentaire';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'listeComment';   //menu qui sera sélect dans la nav du layout


/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try
{
    /**On récupère l'id de l'article à modifier */
    if(array_key_exists('id',$_GET))
    {
        /**Connexion à la bdd */
        $bdd = connexion();
        
        $id = $_GET['id'];
        
        $sth = $bdd->prepare('UPDATE '.DB_PREFIXE.'comment SET c_valide= NOT c_valide WHERE c_id=:id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();

        addFlashBag('Le status du commentaire a bien été modifé');
        header('Location:listeComment.php');
        exit();
    }
}
catch(PDOException $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');