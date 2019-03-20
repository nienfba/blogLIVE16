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

userIsConnected('ROLE_ADMIN');

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
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'user WHERE u_id = :id');
        $sth->execute(['id'=>$id]);
        $user = $sth->fetch(PDO::FETCH_ASSOC);

        if($user)
        {
            //on a bien un utilisateur en base avec cet id
            //On vérifie si l'utilisateur n'a pas d'articles associé !
            //Sinon on empêche sa 
            $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article WHERE a_author = :id');
            $sth->execute(['id'=>$id]);

            if($sth->fetchColumn() > 0)
            {
                addFlashBag('L\'utilisateur a des articles associé. Il ne peut pas être supprimé !','warning');
                addFlashBag('Youpi!','danger');
                addFlashBag('Hello wolrd !');
            }
            else
            {
                //On supprime l'article
                $sth = $bdd->prepare('DELETE FROM '.DB_PREFIXE.'user WHERE u_id = :id');
                $sth->execute(['id'=>$id]);

                addFlashBag('L\'utilisateur a bien été supprimé');
            }
        }
        else
        {
           addFlashBag('Aucun utilisateur n\'a été trouvé avec cet identifiant !','danger');
        }
                    
        //redirection vers la liste des articles (PRG - Post Redirect Get)
        header('Location:listeUser.php');
        exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
    }

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
