<?php
session_start();
/** On veut ajouter un article. On doit soit :
 * 1. Afficher le formulaire d'ajout
 * 2. Récupérer les données du formulaire et les enregistrer ou afficher les erreurs 
 */

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'addCategory.phtml';      //vue qui sera affichée dans le layout
$title = 'Editer une catégorie';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'editCategory';   //menu qui sera sélect dans la nav du layout


/** Variables permettant de réinjectées les données de formulaire
 * On les définies à vide pour le premier affichage du formulaire (pas de Notice not defined ;) )
 */
$id=null; //pas d'id
$titleCategory = '';
$catParent = null;

/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try
{
    $bdd = connexion();

    /** On va récupérer les catégories dans la bdd*/
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
    $categories = orderCategoriesLevel($categories);


    if(array_key_exists('id',$_GET))
    {
        $id = $_GET['id'];
        
        /** On recherche l'article dans la base de données */
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie WHERE c_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $categorie = $sth->fetch(PDO::FETCH_ASSOC);
        
        //$authorId = $article['a_author']; on ne met pas à jour l'auteur initial !
        $titleCategory = $categorie['c_title'];
        $catParent = $categorie['c_parent'];
    }


    /**S'il a des données en entrée */
    if(array_key_exists('id',$_POST))
    {
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        /* Récupération des données de l'article */
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        /* Récupération des données de l'article */
        $id = $_POST['id'];
        $titleCategory = trim($_POST['title']);
        $catParent = ($_POST['categorie']=='')?null:$_POST['categorie'];

        //le formulaire est posté
        if($titleCategory == '')
            $errorForm[] = 'Le titre ne peut-être vide !';

        if(count($errorForm) == 0)
        {
            $sth = $bdd->prepare('UPDATE '.DB_PREFIXE.'categorie SET
            c_title = :title,c_parent=:parent
            WHERE c_id=:id');
            //Liage (bind) des valeurs
            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('title',$titleCategory,PDO::PARAM_STR);
            $sth->bindValue('parent',$catParent,PDO::PARAM_INT);
            if($sth->execute())
                addFlashBag('La catégorie a bien été modifiée');
            else
                addFlashBag('La catégorie n\'a pas pu être modifiée','danger');
            //redirection vers la liste des articles (PRG - Post Redirect Get)
            header('Location:listeCategory.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
        }
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
    $messageErreur= 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');