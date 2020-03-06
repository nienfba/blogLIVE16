<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'category/add';      //vue qui sera affichée dans le layout
$title = 'Ajouter une catégorie';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'addCategory';   //menu qui sera sélect dans la nav du layout


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

    /**S'il a des données en entrée */
    if(array_key_exists('title',$_POST))
    {
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        /* Récupération des données de l'article */
        $titleCategory = trim($_POST['title']);
        $catParent = ($_POST['categorie']=='')?null:$_POST['categorie'];

        //le formulaire est posté
        if($titleCategory == '')
            $errorForm[] = 'Le titre ne peut-être vide !';

        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if(count($errorForm) == 0)
        {
            //Préparation requête
            $sth = $bdd->prepare('INSERT INTO '.DB_PREFIXE.'categorie 
            (cat_id,cat_title,cat_parent)
            VALUES (NULL,:title,:parent)');

            //Liage (bind) des valeurs
            $sth->bindValue('title',$titleCategory,PDO::PARAM_STR);
            $sth->bindValue('parent',$catParent,PDO::PARAM_INT);
            $sth->execute();

            addFlashBag('La catégorie a bien été ajoutée');

            //redirection vers la liste des articles (PRG - Post Redirect Get)
            header('Location:listeCategory.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
        }
    }

}
catch(PDOException $e)
{
    $vue = 'erreur';
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');