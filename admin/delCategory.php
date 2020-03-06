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

        $bdd = connexion();
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie WHERE cat_id = :id');
        $sth->execute(['id'=>$id]);
        $category = $sth->fetch(PDO::FETCH_ASSOC);

        if($category)
        {
            //on a bien une catégorie en base avec cet id
            //On vérifie s'il n'y a pas d'article associé
            $sth = $bdd->prepare('SELECT COUNT(art_id) FROM '.DB_PREFIXE.'article WHERE art_categorie = :id');
            $sth->execute(['id'=>$id]);

            //On vérifie s'il n'y a pas de catégorie enfants
            $sth2 = $bdd->prepare('SELECT COUNT(cat_id) FROM '.DB_PREFIXE.'categorie WHERE cat_parent = :id');
            $sth2->execute(['id'=>$id]);

            if($sth->fetchColumn() > 0)
            {
                addFlashBag('La catégorie a des articles associés. Elle ne peut pas être supprimée !','warning');
            }
            elseif($sth2->fetchColumn() > 0)
            {
                addFlashBag('La catégorie a des catégories enfants. Elle ne peut pas être supprimée !','warning');
            }
            else
            {
                //On supprime la catégorie
                $sth = $bdd->prepare('DELETE FROM '.DB_PREFIXE.'categorie WHERE cat_id = :id');
                $sth->execute(['id'=>$id]);

                addFlashBag('La catégorie a bien été supprimé');
            }
        }
        else
        {
           addFlashBag('Aucune catégorie n\'a été trouvée avec cet identifiant !','danger');
        }
                    
        //redirection vers la liste des articles (PRG - Post Redirect Get)
        header('Location:listeCategory.php');
        exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
    }

}
catch(PDOException $e)
{
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
