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
$title = 'Publier/Dépublier un utilisateur';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'editUser';   //menu qui sera sélect dans la nav du layout


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
        
        /** On recherche l'article dans la base de données */
        /*$sth = $bdd->prepare('SELECT u_valide FROM '.DB_PREFIXE.'user WHERE u_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);*/
        
        $sth = $bdd->prepare('UPDATE '.DB_PREFIXE.'user SET u_valide= NOT u_valide WHERE u_id=:id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();

        addFlashBag('Le status de l\'utilisateur a bien été modifé');
        header('Location:listeUser.php');
        exit();
    }
}
catch(PDOException $e)
{
    /** On affiche une autre vue car ici l'erreur est critique. 
     * Dans l'avenir il faudra ici envoyer un email à l'admin par exemple car il n'est pas normal d'avoir une erreur de connexion au 
     * serveur ou une erreur SQL !
     */
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');