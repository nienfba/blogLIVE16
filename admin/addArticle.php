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
$vue = 'addArticle.phtml';      //vue qui sera affichée dans le layout
$title = 'Ajouter un article';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'addArticle';   //menu qui sera sélect dans la nav du layout


/** Variables permettant de réinjectées les données de formulaire
 * On les définies à vide pour le premier affichage du formulaire (pas de Notice not defined ;) )
 */
$id=null; //pas d'id
$titleArticle = '';
$datePublished = new DateTime();
$contentArticle = '';
$valideArticle = 1;
$catArticle = 0;
$pictureArticle = '';
$pictureDisplay = false;

/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try
{
    /** Notre programme vérifie les entrées POST ET GET pour savoir s'il reçoit des données 
     * Mais d'abord il nous faut savoir si nous avons besoin de choses communes que l'on ai des entrées ou pas ?
     * Ici c'est le cas ?
     * 1. Que doit-on afficher dans le formulaire (liste des catégories !)
     * 2. Que se passe-t-il s'il y a des erreurs dans le formulaire : on réaffiche le formulaire !
     * 
     * =>   Il nous faut donc la liste des catégories même s'il y a des données en entrées ou pas !
    */
    //var_dump($_SERVER);
    
    $bdd = connexion();

    /** On va récupérer les catégories dans la bdd*/
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

    //On ordonne les catégories par ordre hirarchique avec un niveau (ATTENTION niveau avancé...)
    $categories = orderCategoriesLevel($categories);


    /**S'il a des données en entrée */
    if(array_key_exists('title',$_POST))
    {
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        /* Récupération des données de l'article */
        $authorId = 1; //pour le moment à 1 (on récupèrera cette donnée plus tard)
        $titleArticle = trim($_POST['title']);
        $dateArticle = $_POST['date'];
        $timeArticle = $_POST['time'];
        $datePublished = new DateTime($dateArticle.' '.$timeArticle);
        $catArticle = $_POST['categorie'];
        $contentArticle = trim($_POST['content']);
        $valideArticle = isset($_POST['valide'])?true:false;
        $pictureArticle = NULL; //On met l'image à NULL en attendant de voir si une image est transmise ! 

        //le formulaire est posté
        if($titleArticle == '')
            $errorForm[] = 'Le titre ne peut-être vide !';

        if($datePublished===false)
            $errorForm[] = 'La date de publication est erronée !';
        
        //var_dump($_POST);
        /** Récupérer l'image et la déplacer ! */
        if($_FILES['picture']["tmp_name"] != '')
        {
            $tmpPicture = uploadFile('picture','articles');
            if(!$tmpPicture)
                $errorForm[] = 'Une erreur s\'est produite lors de l\'upload de l\'image !';
            else
                $pictureArticle = $tmpPicture;
        }

        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if(count($errorForm) == 0)
        {
            //Préparation requête
            $sth = $bdd->prepare('INSERT INTO '.DB_PREFIXE.'article 
            (a_id,a_title,a_date_published,a_date_created,a_content,a_picture,a_categorie,a_author,a_valide)
            VALUES (NULL,:title,:datePublished,NOW(),:content,:picture,:categorie,:author,:valide)');

            //Liage (bind) des valeurs
            $sth->bindValue('title',$titleArticle,PDO::PARAM_STR);
            $sth->bindValue('datePublished',$datePublished->format('Y-m-d H:i:s'));
            $sth->bindValue('content',$contentArticle,PDO::PARAM_STR);
            $sth->bindValue('picture',$pictureArticle,PDO::PARAM_STR);
            $sth->bindValue('categorie',$catArticle,PDO::PARAM_INT);
            $sth->bindValue('author',$authorId,PDO::PARAM_INT);
            $sth->bindValue('valide',$valideArticle,PDO::PARAM_BOOL);
            $sth->execute();

            addFlashBag('L\'article a bien été ajouté');

            //redirection vers la liste des articles (PRG - Post Redirect Get)
            header('Location:listeArticle.php');
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
   //$vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $errorForm[] = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');