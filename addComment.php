<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('lib/app.lib.php');


/** On définie nos variables nécessaire pour la vue et le layout */
$vue = '';      //vue qui sera affichée dans le layout
$title = '';  //titre de la page qui sera mis dans title et h1 dans le layout


/** Variables permettant de réinjectées les données de formulaire
 * On les définies à vide pour le premier affichage du formulaire (pas de Notice not defined ;) )
 */
$idArticle=null; //pas d'id
$name = '';
$email = '';
$message = '';
$error=false;


/** Maintenant on gère le fonctionnement de la page
 * Block try pour essayer tout le programme
 */
try {
    

    /**S'il a des données en entrée */
    if (array_key_exists('idArticle', $_POST)) {
        //Pas d'erreur pour le moment sur les données

        $bdd = connexion();

        /* Récupération des données du commentaire */
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $message = trim($_POST['message']);
        $idArticle = $_POST['idArticle'];

        if ($email == '' || $name == '' || $message == '') {
            addFlashBag('Merci de remplir tous les champs pour poster un commentaire !','danger');
            $error = true;
        }

        if(!ctype_alnum($name))
        {
            addFlashBag('Votre nom doit contenir uniquement des lettres et des chiffres', 'danger');
            $error = true;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            addFlashBag('Votre email n\'est pas correct !','danger');
            $error = true;
        }

        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if (!$error) {

            //Préparation requête
            $sth = $bdd->prepare('INSERT INTO '.DB_PREFIXE.'comment (c_content,c_pseudo,c_email,c_date_posted,c_valide,c_article) VALUES (:content,:name,:email,NOW(),0,:idArticle)');

            //Liage (bind) des valeurs
            $sth->bindValue('content', $message, PDO::PARAM_STR);
            $sth->bindValue('name', $name, PDO::PARAM_STR);
            $sth->bindValue('email', $email, PDO::PARAM_STR);
            $sth->bindValue('idArticle', $idArticle, PDO::PARAM_STR);

            $sth->execute();

            addFlashBag('Votre commentaire a bien été pris en compte. Il sera publié dès validation par notre équipe de modérateur.');
        }
        else
        {
            // On injecte les données POST dans la session pour les récupérer dans le formulaire
            // comme nous avons une redirection même si des erreurs existent !
            $_SESSION['comment'] = $_POST;
        }
    }
    //redirection vers la liste des articles (PRG - Post Redirect Get)
    header('Location:detailArticle.php?id='.$idArticle.'#comment');
    exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !

} catch (PDOException $e) {
    $vue = 'erreur';
    $messageErreur  = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
    include('tpl/layout.phtml');
}

