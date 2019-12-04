<?php
require_once('Controller.php');

class ArticleController extends Controller
{

    public function add()
    {

        $modelCategorie = new Categorie();
        $categories = $modelCategorie->list();

        //On ordonne les catégories par ordre hirarchique avec un niveau (ATTENTION niveau avancé...)
        $categories = orderCategoriesLevel($categories);


        /**S'il a des données en entrée */
        if (array_key_exists('title', $_POST)) {
            $errorForm = []; //Pas d'erreur pour le moment sur les données

            /* Récupération des données de l'article */
            $authorId = 1; //pour le moment à 1 (on récupèrera cette donnée plus tard)
            $titleArticle = trim($_POST['title']);
            $dateArticle = $_POST['date'];
            $timeArticle = $_POST['time'];
            $datePublished = new DateTime($dateArticle . ' ' . $timeArticle);
            $catArticle = $_POST['categorie'];
            $contentArticle = trim($_POST['content']);
            $valideArticle = isset($_POST['valide']) ? true : false;
            $pictureArticle = NULL; //On met l'image à NULL en attendant de voir si une image est transmise ! 

            //le formulaire est posté
            if ($titleArticle == '')
                $errorForm[] = 'Le titre ne peut-être vide !';

            if ($datePublished === false)
                $errorForm[] = 'La date de publication est erronée !';

            //var_dump($_POST);
            /** Récupérer l'image et la déplacer ! */
            if ($_FILES['picture']["tmp_name"] != '') {
                $tmpPicture = uploadFile('picture', 'articles');
                if (!$tmpPicture)
                    $errorForm[] = 'Une erreur s\'est produite lors de l\'upload de l\'image !';
                else
                    $pictureArticle = $tmpPicture;
            }

            /** Si j'ai pas d'erreur j'insert dans la bdd */
            if (count($errorForm) == 0) {
                $modelArticle = new Article();
                $modelArticle->add($titleArticle, $datePublished, $contentArticle, $pictureArticle, $catArticle,  $authorId, $valideArticle);

                addFlashBag('L\'article a bien été ajouté');

                //redirection vers la liste des articles (PRG - Post Redirect Get)
                header('Location:listeArticle.php');
                exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
            }
        }
        include('tpl/layout.phtml');
    }

}