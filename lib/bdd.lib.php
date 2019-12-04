 <?php

/** Connexion à la base de données via PDO
 * @param void
 * @return PDO connexion vers la base de données
 */
function connexion():PDO
{
    $dbh = new PDO(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, DB_USER, DB_PASSWORD);
    //On dit à PDO de nous envoyer une exception s'il n'arrive pas à se connecter ou s'il rencontre une erreur
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $dbh;
}


/** Trouve un article dans la base
 * @param {int} $id id de l'élément à chercher
 * @return {array}
 */
function findArticle(int $id)
{
    $bdd = connexion();
    
    /** On recherche l'article dans la base de données */
    $sth = $bdd->prepare('SELECT * FROM '.DB_PREFIXE.'article WHERE a_id = :id');
    $sth->bindValue('id', $id, PDO::PARAM_INT);
    $sth->execute();
    $article = $sth->fetch(PDO::FETCH_ASSOC);

    return $article;
}



/** Mise à jour d'un article
 * @param int $id id de l'article
 * @param string $titleArticle titre de l'article
 * ... TODO
 * @return void
 */
function updateArticle(int $id, string $titleArticle, DateTime $datePublished, string $contentArticle, ?string $pictureArticle, int $catArticle, ?bool $valideArticle=false):void
{
    $bdd = connexion();

    //Préparation requête
    $sth = $bdd->prepare('UPDATE ' . DB_PREFIXE . 'article SET a_title = :title ,a_date_published=:datePublished,
            a_content=:content,a_picture=:picture,a_categorie=:categorie,a_valide=:valide WHERE a_id=:id');

    //Liage (bind) des valeurs
    $sth->bindValue('id', $id, PDO::PARAM_INT);
    $sth->bindValue('title', $titleArticle, PDO::PARAM_STR);
    $sth->bindValue('datePublished', $datePublished->format('Y-m-d H:i:s'));
    $sth->bindValue('content', $contentArticle, PDO::PARAM_STR);
    $sth->bindValue('picture', $pictureArticle, PDO::PARAM_STR);
    $sth->bindValue('categorie', $catArticle, PDO::PARAM_INT);
    $sth->bindValue('valide', $valideArticle, PDO::PARAM_BOOL);
    $sth->execute();
}

/** Mise à jour du status article
 * @param int $valide id de l'article
 * @param string $titleArticle titre de l'article

 * @return void
 */
function updateStatusArticle(int $id, bool $valide)
{
    $bdd = connexion();
    $sth = $bdd->prepare('UPDATE ' . DB_PREFIXE . 'article SET a_valide=:valide WHERE a_id=:id');
    $sth->bindValue('id', $id, PDO::PARAM_INT);
    $sth->bindValue('valide', $valide, PDO::PARAM_INT);
    $sth->execute();
}


/** Récupère la liste des articles
 * @param void
 * @return array
 */
function listArticle():array
{
    $bdd = connexion();
    $sth = $bdd->prepare('SELECT * FROM ' . DB_PREFIXE . 'article INNER JOIN ' . DB_PREFIXE . 'user ON a_author=u_id LEFT JOIN ' . DB_PREFIXE . 'categorie ON a_categorie=c_id');
    $sth->execute();

    $articles = $sth->fetchAll(PDO::FETCH_ASSOC);

    return $articles;
}

/** Insertion d'un article
 * @param int $id id de l'article
 * @param string $titleArticle titre de l'article
 * ... TODO
 * @return int l'identifiant clef primaire de l'article inséré
 */
function addArticle(string $titleArticle, DateTime $datePublished, string $contentArticle, ?string $pictureArticle, int $catArticle, int $authorId, ?bool $valideArticle = false)
{
    $bdd = connexion();
    //Préparation requête
    $sth = $bdd->prepare('INSERT INTO ' . DB_PREFIXE . 'article 
            (a_id,a_title,a_date_published,a_date_created,a_content,a_picture,a_categorie,a_author,a_valide)
            VALUES (NULL,:title,:datePublished,NOW(),:content,:picture,:categorie,:author,:valide)');

    //Liage (bind) des valeurs
    $sth->bindValue('title', $titleArticle, PDO::PARAM_STR);
    $sth->bindValue('datePublished', $datePublished->format('Y-m-d H:i:s'));
    $sth->bindValue('content', $contentArticle, PDO::PARAM_STR);
    $sth->bindValue('picture', $pictureArticle, PDO::PARAM_STR);
    $sth->bindValue('categorie', $catArticle, PDO::PARAM_INT);
    $sth->bindValue('author', $authorId, PDO::PARAM_INT);
    $sth->bindValue('valide', $valideArticle, PDO::PARAM_BOOL);
    $sth->execute();

    return $bdd->lastInsertId();
}

/** Supprime un article
 * @param int $id id de l'article
 
 * @return void
 */
function delArticle(int $id)
{
    $bdd = connexion();
    //On supprime l'article
    $sth = $bdd->prepare('DELETE FROM ' . DB_PREFIXE . 'article WHERE a_id = :id');
    $sth->execute(['id' => $id]);

}


/** Récupère la liste des catégories
 * @param void
 * @return array
 */
function listCategory():array
{
    $bdd = connexion();

    /** On va récupérer les catégories dans la bdd*/
    $sth = $bdd->prepare('SELECT * FROM ' . DB_PREFIXE . 'categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

    return $categories;
}

function listParentOrderedCategory()
{
    $bdd = connexion();
    
    $sth = $bdd->prepare('SELECT c1.c_id, c1.c_title, c2.c_title as parent, c1.c_parent, COUNT(a.a_id) as articles  FROM ' . DB_PREFIXE . 'categorie c1 LEFT JOIN ' . DB_PREFIXE . 'categorie c2 ON c1.c_parent=c2.c_id LEFT JOIN ' . DB_PREFIXE . 'article a ON c1.c_id = a.a_categorie GROUP BY c1.c_id,c2.c_id ORDER BY c1.c_title, c1.c_parent');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}