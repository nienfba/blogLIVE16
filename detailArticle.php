<?php
session_start();
/**On inclu d'abord le fichier de configuration */
include('config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
include('lib/app.lib.php');

/** On définie nos variables nécessaire pour la vue et le layout */
$vue = 'detail';      //vue qui sera affichée dans le layout
$subTitle = '';  //titre de la page qui sera mis dans title et h1 dans le layout


$nameComment = '';
$emailComment = '';
$contentComment = '';

try {
    if (array_key_exists('id', $_GET)) {
        $id = $_GET['id'];

        $flashbag = getFlashBag();
    
        $bdd = connexion();
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article INNER JOIN '.DB_PREFIXE.'user ON a_author=u_id LEFT JOIN '.DB_PREFIXE.'categorie ON a_categorie=c_id WHERE a_id = :id AND a_valide=1');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            $title = $article['a_title'];
            $bgImage = UPLOADS_URL.'articles/'.$article['a_picture'];
            $postedBy = $article['u_firstname']. ' '.$article['u_lastname'] ;
            $postedById = $article['u_id'];
            $postedDate = (new dateTime($article['a_date_published']))->format('d/m/Y à H:i');

            $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'comment WHERE c_article = :idArticle AND c_valide=1 ORDER BY c_date_posted DESC'); 
            $sth->bindValue('idArticle', $id, PDO::PARAM_INT);
            $sth->execute();
            $comments = $sth->fetchAll(PDO::FETCH_ASSOC);

            // Si on revient ici avec des données de commentaire et une erreur
            if(isset($_SESSION['comment']))
            {
                $nameComment = trim($_SESSION['comment']['name']);
                $emailComment = trim($_SESSION['comment']['email']);
                $contentComment = trim($_SESSION['comment']['message']);
                unset($_SESSION['comment']);
            }
        }
        else
           header('Location:404.php');
    }
    else
        header('Location:404.php');

} catch (PDOException $e) {
    $vue = 'erreur';
    $title="Erreur !";
    $bgImage = 'img/error.png';
    //Si une exception est envoyée par PDO (exemple : serveur de BDD innaccessible) on arrive ici
    $messageErreur = 'Une erreur de connexion a eu lieu :'.$e->getMessage();
}

include('tpl/layout.phtml');
