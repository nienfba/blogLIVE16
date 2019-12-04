<?php
require_once('../lib/bdd.lib.php');

abstract class Model
{
    protected $bdd;
    protected $table;

    public function __construct()
    {
        $this->bdd = connexion();
    }


    /** Trouve une catégorie dans la base
     * @param {int} $id id de l'élément à chercher
     * @return {array}
     */
    public function find($id)
    {
        /** On recherche l'article dans la base de données */
        $sth = $this->bdd->prepare('SELECT * FROM ' . DB_PREFIXE . $this->table. ' WHERE c_id = :id');
        $sth->bindValue('id', $id, PDO::PARAM_INT);
        $sth->execute();
        $item = $sth->fetch(PDO::FETCH_ASSOC);
        
        return $item;
    }

    /** Récupère la liste des articles
     * @param void
     * @return array
     */
    public function list(): array
    {
        /** On va récupérer les catégories dans la bdd*/
        $sth = $this->bdd->prepare('SELECT * FROM ' . DB_PREFIXE . $this->table);
        $sth->execute();
        $items = $sth->fetchAll(PDO::FETCH_ASSOC);

        return $items;
    }

}