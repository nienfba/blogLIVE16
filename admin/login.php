<?php
session_start();

/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('../lib/app.lib.php');




/** On test si on réceptionne les données de login */
try
{
    if(array_key_exists('email',$_POST))
    {
        //on vérifie si l'utilisateur existe en base (email)
        //on compare ensuite sont mot de passe avec celui en base (password_verify())
        //si tout est ok on créer 2 index dans $_SESSION - $_SESSION['connected'] = true; $_SESSION['user'] = ['id'=>...,'name'=>...,'role'=>...];
        //sinon on affiche de nouveau le formulaire avec un message d'erreur (Impossible de se connecter ! Vérfier login + mot de passe)
    }
    

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

/** On inclu directement la vue login qui est un layout complet ! 
 * Spécifique pour le login... pas de menu... 
 *
 */
include('tpl/login.phtml');


/*
if(isset($_SESSION['connected']) && $_SESSION['connected'] === true)
    header('Location:index.php');

//Si l'utilisateur a les droits !!! 
$_SESSION['connected'] = true;
$_SESSION['user'] = ['id'=>10,'prenom'=>'Fabien'];*/




?>