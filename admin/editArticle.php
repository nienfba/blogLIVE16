<?php
/** On veut éditer un article. On doit soit :
 * 1. Afficher le formulaire d'édition avec le contenu de l'article pré-rempli
 * 2. Récupérer les données du formulaire et les enregistrer en mettant à jour la bdd ou afficher les erreurs 
 */

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');

userIsConnected();

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'addArticle.phtml';      //vue qui sera affichée dans le layout - m^me vue que l'ajout
$title = 'Editer un article';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'editArticle';   //menu qui sera sélect dans la nav du layout



$pictureDisplay = false; //on affiche pas l'image sauf si elle existe sur le disque (voir plus bas)

/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try
{
    /**Connexion à la bdd */
    $bdd = connexion();

    /** On va récupérer les catégories dans la bdd*/
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

    //On ordonne les catégories par ordre hirarchique avec un niveau (ATTENTION niveau avancé...)
    $categories = orderCategoriesLevel($categories);

    /**On récupère l'id de l'article à modifier */
    if(array_key_exists('id',$_GET))
    {
        $id = $_GET['id'];
        
        /** On recherche l'article dans la base de données */
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article WHERE a_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);
        
        //$authorId = $article['a_author']; on ne met pas à jour l'auteur initial !
        $titleArticle = $article['a_title'];
        $datePublished = new DateTime($article['a_date_published']);
        $contentArticle = $article['a_content'];
        $valideArticle = $article['a_valide'];
        $catArticle = $article['a_categorie'];
        $pictureArticle = $article['a_picture'];

        /** On va injectée ces données dans la vue. Comme la vue est la même que l'ajout on va paramètrer le formulaire si l'id n'est pas nul
         * On va donc rajouter un $id=null dans le programme d'ajout
         */
    }

    /**S'il a des données en entrée - Le formulaire est posté*/
    if(array_key_exists('title',$_POST))
    {
        var_dump($_POST);
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        $id = $_POST['id']; //on récupère l'id de l'article

        /* Récupération des données de l'article */
        $titleArticle = trim($_POST['title']);
        $dateArticle = $_POST['date'];
        $timeArticle = $_POST['time'];
        $datePublished = new DateTime($dateArticle.' '.$timeArticle);
        $catArticle = $_POST['categorie'];
        $contentArticle = trim($_POST['content']);
        $valideArticle = isset($_POST['valide'])?true:false;
        $pictureArticle = isset($_POST['oldPicture'])?$_POST['oldPicture']:null; //On met l'image à NULL en attendant de voir si une image est transmise ! 

        //le formulaire est posté
        if($titleArticle == '')
            $errorForm[] = 'Le titre ne peut-être vide !';

        if($datePublished===false)
            $errorForm[] = 'La date de publication est erronée ou antérieur à la date du jour !';
        
        if($_FILES['picture']["tmp_name"]!= '')
        {
            $tmpNewPicture = uploadFile('picture','articles');
            if(!$tmpNewPicture)
                $errorForm[] = 'Une erreur s\'est produite lors de l\'upload de l\'image !';
            else
            {
                //On supprime l'ancienne image
                delFile(UPLOADS_DIR.'articles/'.$pictureArticle);
                
                $pictureArticle = $tmpNewPicture;
               
            }
        }

        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if(count($errorForm) == 0)
        {
            //Préparation requête
            $sth = $bdd->prepare('UPDATE '.DB_PREFIXE.'article SET a_title = :title ,a_date_published=:datePublished,
            a_content=:content,a_picture=:picture,a_categorie=:categorie,a_valide=:valide WHERE a_id=:id');

            //Liage (bind) des valeurs
            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('title',$titleArticle,PDO::PARAM_STR);
            $sth->bindValue('datePublished',$datePublished->format('Y-m-d H:i:s'));
            $sth->bindValue('content',$contentArticle,PDO::PARAM_STR);
            $sth->bindValue('picture',$pictureArticle,PDO::PARAM_STR);
            $sth->bindValue('categorie',$catArticle,PDO::PARAM_INT);
            $sth->bindValue('valide',$valideArticle,PDO::PARAM_BOOL);
            $sth->execute();

            //redirection vers la liste des articles (PRG - Post Redirect Get)
            addFlashBag('L\'article a bien été modifé');
            header('Location:listeArticle.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
        }
    }

    /** On vérifie si l'image existe sur le disque pour la passer à la vue */
    if(file_exists(UPLOADS_DIR.'articles/'.$pictureArticle) && $pictureArticle != null)
        $pictureDisplay = true;

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