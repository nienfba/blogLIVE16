<?php

class Article 
{

    protected $id;


    protected $title;

    /**
     * @var User auteur de l'article
     */
    protected $author;


    const TEST = 1; 


    public static $essai = 42;


    public function __construct($id=null)
    {
        $this->id = $id;

        $this->bdd = new PDO(DB_DSN,DB_USER,DB_PASS);
        //On dit à PDO de nous envoyer une exception s'il n'arrive pas à se connecter ou s'il rencontre une erreur
        $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if($this->id != null)
           $this->load(); 
    }
    

    public function affiche()
    {
        echo self::$essai;
    }


    public function load()
    {
          /** On recherche l'article dans la base de données */
        $sth =  $this->bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article WHERE a_id = :id');
        $sth->bindValue('id',$this->id,PDO::PARAM_INT);
        $sth->execute();
        $article = $sth->fetch(PDO::FETCH_ASSOC);
        
        //$authorId = $article['a_author']; on ne met pas à jour l'auteur initial !
        $this->title = $article['a_title'];
       /*  $datePublished = new DateTime($article['a_date_published']);
        $contentArticle = $article['a_content'];
        $valideArticle = $article['a_valide'];
        $catArticle = $article['a_categorie'];
        $pictureArticle = $article['a_picture']; */

        $this->author = new User($article['a_author']);

    }
    

    /**
     * Get auteur de l'article
     *
     * @return  User
     */ 
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set auteur de l'article
     *
     * @param  User  $author  auteur de l'article
     *
     * @return  self
     */ 
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }



    public static function getAllArticles()
    {
        $bdd = new PDO(DB_DSN,DB_USER,DB_PASS);
        //On dit à PDO de nous envoyer une exception s'il n'arrive pas à se connecter ou s'il rencontre une erreur
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article');
        $sth->execute();

        $articles = $sth->fetchAll(PDO::FETCH_ASSOC);

        $collectionArticles = array();
        foreach($articles as $article)
            $collectionArticles[] = new Article($article['a_id']);

        return $collectionArticles;
    }
}