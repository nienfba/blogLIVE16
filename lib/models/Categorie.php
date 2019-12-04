<?php
require_once('../lib/bdd.lib.php');
require_once('Model.php');

class Categorie extends Model
{
    protected $table = 'categorie';

    public function update()
    { }

    public function add()
    { }

    /** Récupère la liste des catégories
     * @param void
     * @return array
     */
    public function list(): array
    {
        /** On va récupérer les catégories dans la bdd*/
        $sth = $this->bdd->prepare('SELECT * FROM ' . DB_PREFIXE . 'categorie');
        $sth->execute();
        $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $categories;
    }




  

    public function listParentOrdered()
    {
        $sth = $this->bdd->prepare('SELECT c1.c_id, c1.c_title, c2.c_title as parent, c1.c_parent, COUNT(a.a_id) as articles  FROM ' . DB_PREFIXE . 'categorie c1 LEFT JOIN ' . DB_PREFIXE . 'categorie c2 ON c1.c_parent=c2.c_id LEFT JOIN ' . DB_PREFIXE . 'article a ON c1.c_id = a.a_categorie GROUP BY c1.c_id,c2.c_id ORDER BY c1.c_title, c1.c_parent');
        $sth->execute();
        $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $categories;
    }
}