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
include('../lib/bdd.lib.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = '';      //vue qui sera affichée dans le layout
$title = '';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = '';   //menu qui sera sélect dans la nav du layout

try
{

    if(array_key_exists('id',$_GET))
    {
        $id = $_GET['id'];

      
        $article = findArticle($id);

        if($article)
        {
            //on a bien un article en base avec cet id

            //on supprime l'image si elle existe sur le disque
            delFile(UPLOADS_DIR.'articles/'.$article['a_picture']);

            delArticle($id);

            addFlashBag('L\'article a bien été supprimé');
            //redirection vers la liste des articles (PRG - Post Redirect Get)
           
        }
        else
        {
            addFlashBag('Article introuvable !');
        }

    }
    else
    {
        addFlashBag('Pas d\'identifiant fourni !');
    }
    header('Location:listeArticle.php');
    exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
