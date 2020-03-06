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

userIsConnected('ROLE_ADMIN');

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'user/add';      //vue qui sera affichée dans le layout
$title = 'Editer un utilisateur';  //titre de la page qui sera mis dans title et h1 dans le layout
$menuSelected = 'editUser';   //menu qui sera sélect dans la nav du layout


/** Variables permettant de réinjectées les données de formulaire
 * On les définies à vide pour le premier affichage du formulaire (pas de Notice not defined ;) )
 */
$id=null; //pas d'id
$firstnameUser = '';
$lastnameUser = '';
$emailUser  = '';
$passwordUser = '';
$valideUser = 1;
$roleUser = 'ROLE_AUTHOR';

/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try
{
    $bdd = connexion();

    if(array_key_exists('id',$_GET))
    {
        $id = $_GET['id'];
        
        /** On recherche l'article dans la base de données */
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'user WHERE use_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);
        
        //$authorId = $article['a_author']; on ne met pas à jour l'auteur initial !
        $firstnameUser = $article['use_firstname'];
        $lastnameUser = $article['use_lastname'];
        $emailUser = $article['use_email'];
        $valideUser = $article['use_valide'];
        $roleUser  = $article['use_role'];
    }


    /**S'il a des données en entrée */
    if(array_key_exists('id',$_POST))
    {
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        /* Récupération des données de l'article */
        $id = $_POST['id'];
        $firstnameUser = trim($_POST['firstname']);
        $lastnameUser = $_POST['lastname'];
        $emailUser = $_POST['email'];
        $passwordUser = $_POST['password'];
        $passwordConfUser = $_POST['passwordConf'];
        $valideUser = isset($_POST['valide'])?true:false;
        $roleUser = $_POST['role'];

        //le formulaire est posté
        if($emailUser == '')
            $errorForm[] = 'L\'email ne peut-être vide !';

        if($passwordUser != '' && $passwordUser != $passwordConfUser)
            $errorForm[] = 'Le mot de passe ou sa confimation ne sont pas corrects !';
        
        
        if($passwordUser != '' && strlen($passwordUser) < 8)
            $errorForm[] = 'Le mot de passe doit comporter 8 caractères minimum !';
        
        //On vérifie si l'utilisateur n'est pas déjà dans la base avec cet email (champ unique email !!)

        /**On vérifie qu'un utilisateur n'est pas dans la base avec l'email modifié !*/
        $sth = $bdd->prepare('SELECT use_email FROM '.DB_PREFIXE.'user WHERE use_email = :email');
        $sth->bindValue('email',$emailUser,PDO::PARAM_STR);
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if($user != false)
            $errorForm[] = '<br> Un utilisateur existe déjà avec cet email.';
    
        /** Si j'ai pas d'erreur je met à jour dans la bdd */
        if(count($errorForm) == 0)
        {
            //HAsh du mot de passe
            if($passwordUser != '')
            {
                $passwordUser = password_hash($passwordUser,PASSWORD_DEFAULT);
                $sth = $bdd->prepare('UPDATE '.DB_PREFIXE.'user SET
                use_firstname = :firstname,use_lastname=:lastname,use_email=:email,use_password=:password, use_valide=:valide,use_role=:role 
                WHERE use_id=:id');
                $sth->bindValue('password',$passwordUser,PDO::PARAM_STR);
                addFlashBag('L\'utilisateur et son mot de passe ont bien été modifiés');
            }
            else
            {
                //Préparation requête
                $sth = $bdd->prepare('UPDATE '.DB_PREFIXE.'user SET 
                use_firstname = :firstname,use_lastname=:lastname,use_email=:email,use_valide=:valide,use_role=:role
                WHERE use_id=:id');
                addFlashBag('L\'utilisateur a bien été modifié');
            }

            //Liage (bind) des valeurs
            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('firstname',$firstnameUser,PDO::PARAM_STR);
            $sth->bindValue('lastname',$lastnameUser,PDO::PARAM_STR);
            $sth->bindValue('email',$emailUser,PDO::PARAM_STR);
            $sth->bindValue('valide',$valideUser,PDO::PARAM_BOOL);
            $sth->bindValue('role',$roleUser,PDO::PARAM_STR);
            $sth->execute();

            //redirection vers la liste des articles (PRG - Post Redirect Get)
            header('Location:listeUser.php');
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
    $vue = 'erreur';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');