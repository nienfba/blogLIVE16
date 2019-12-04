<?php


class HomeController extends Controller
{
    public function dashboard()
    {
        $this->setView('index.phtml');
        $this->setTitle('Dashborad');

        $modelCategorie = new Categorie();
        $categories = $modelCategorie->list();
        //On ordonne les catégories par ordre hirarchique avec un niveau (ATTENTION niveau avancé...)
        $categories = orderCategoriesLevel($categories);

        $modelArticle = new Article();
        $articles = $modelArticle->findLast(5);

        $this->render(['categories'=>$categories,'articles'=>$articles]);
    }
}