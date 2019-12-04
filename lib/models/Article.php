<?php
require_once('../lib/bdd.lib.php');
require_once('Model.php');
class Article extends Model
{
    protected $table = 'article';

    /** Mise à jour d'un article
     * @param int $id id de l'article
     * @param string $titleArticle titre de l'article
     * ... TODO
     * @return void
     */
    public function update(int $id, string $titleArticle, DateTime $datePublished, string $contentArticle, ?string $pictureArticle, int $catArticle, ?bool $valideArticle = false): void
    {

        //Préparation requête
        $sth = $this->bdd->prepare('UPDATE ' . DB_PREFIXE . 'article SET a_title = :title ,a_date_published=:datePublished,
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
    public function updateStatus(int $id, bool $valide)
    {
        $sth = $this->bdd->prepare('UPDATE ' . DB_PREFIXE . 'article SET a_valide=:valide WHERE a_id=:id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->bindValue('valide', $valide, PDO::PARAM_INT);
        $sth->execute();
    }


    /** Récupère la liste des articles
     * @param void
     * @return array
     */
    public function list(): array
    {
        $sth = $this->bdd->prepare('SELECT * FROM ' . DB_PREFIXE . 'article INNER JOIN ' . DB_PREFIXE . 'user ON a_author=u_id LEFT JOIN ' . DB_PREFIXE . 'categorie ON a_categorie=c_id');
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
    public function add(string $titleArticle, DateTime $datePublished, string $contentArticle, ?string $pictureArticle, int $catArticle, int $authorId, ?bool $valideArticle = false)
    {
        //Préparation requête
        $sth = $this->bdd->prepare('INSERT INTO ' . DB_PREFIXE . 'article 
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

        return $this->bdd->lastInsertId();
    }

    /** Supprime un article
     * @param int $id id de l'article
 
     * @return void
     */
    public function del(int $id)
    {
        //On supprime l'article
        $sth = $this->bdd->prepare('DELETE FROM ' . DB_PREFIXE . 'article WHERE a_id = :id');
        $sth->execute(['id' => $id]);
    }

    /** Retourne les $nb derniers article
     * @param int $nb le nombr d'articles à retourner
 
     * @return void
     */
    public function findLast(int $nb)
    {
        $sth = $this->bdd->query('SELECT * FROM ' . DB_PREFIXE . 'article INNER JOIN ' . DB_PREFIXE . 'user ON a_author=u_id LEFT JOIN ' . DB_PREFIXE . 'categorie ON a_categorie=c_id ORDER BY a_date_created DESC LIMIT 1,'. $nb);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

}
