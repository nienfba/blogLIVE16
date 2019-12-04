<?php
session_start();
/** On veut modifier juste la publication de l'article
 * On pourrait même appeler cette page en AJAX mais pour le moment on le faire avec une requête HTTP classique 
 */

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');
include('../lib/bdd.lib.php');
include('../lib/models/Article.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = '';      //vue qui sera affichée dans le layout - m^me vue que l'ajout
$title = 'Publier/Dépublier un article';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'editArticle';   //menu qui sera sélect dans la nav du layout




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

        $modelArticle = new Article();
        $article = $modelArticle->find($id);

        $modelArticle->updateStatus($id, !$article['a_valide']);

        addFlashBag('La publication de l\'article a bien été modifé');
        header('Location:listeArticle.php');
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