<?php

/** Permet d'afficher tous les noms des colonnes d'une table 
 * @param string $table nom de la table dans la base de données
 * @param PDO $dbh objet de connexion vers la base
 * @return string liste séparée par une virgule des noms des colonnes
*/
function getColumnName($table,$dbh)
{
    $q = $dbh->prepare("DESCRIBE ".$table);
    $q->execute();
    $table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
    $stringFields = '';
    foreach($table_fields as $field)
        $stringFields .= $field.',';

    return $stringFields;
}

/** Déplace un fichier transmis dans un répertoire du serveur
 * @param $postName nom de l'index dans le tableau $_FILES
 * @param $folder chemin absolue ou relatif où le fichier sera déplacé
 * @return mixed nom du fichier ou false si l'upload a échoué
  */
function uploadFile($postName, $folder)
{
    if ($_FILES[$postName]["error"] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$postName]["tmp_name"];
        // basename() peut empêcher les attaques de système de fichiers;
        // la validation/assainissement supplémentaire du nom de fichier peut être approprié
        $name = uniqid().'-'.basename($_FILES[$postName]["name"]);
        if(move_uploaded_file($tmp_name, UPLOADS_DIR.$folder.'/'.$name))
            return $name;
    }
    
    return false;
}

/** Supprime un fichier sur le disque
 * @param $file chemin absolu ou relatif d'un fichier sur le disque du serveur
 * @return boolean true si suppression ou false sinon
 */
function delFile($file)
{
    if(file_exists($file) && !is_dir($file))
    {
        if(unlink($file))
            return true;
    }
    return false;
}

/** function addFlashBag
 * Ajoute une valeur au flashbag
 * @param string $texte le message a afficher
 * @param string $level le niveau du message (correspond au type d'info bulle boostrap : success - warning - danger ...)
 * @return void
 */
function addFlashBag($texte,$level = 'success')
{
    if(!isset($_SESSION['flashbag']) || !is_array($_SESSION['flashbag']))
        $_SESSION['flashbag'] = array();

    $_SESSION['flashbag'][] = ['message'=>$texte, 'level'=>$level];
}

/** function getFlashBag
 * Ajoute une valeur au flashbag
 * @param void
 * @return array flashbag le tableau contenant tous les messages a afficher
 */
function getFlashBag()
{
    if(isset($_SESSION['flashbag']) && is_array($_SESSION['flashbag']))
    {
        $flashbag = $_SESSION['flashbag'];
        unset($_SESSION['flashbag']);
        return $flashbag;
    }
    return false;
}


/** function userIsConnected
 * Vérifie si l'utilisateur est connecté avec le bon rôle ! 
 * Redirige vers la page de login sinon 
 * @param string $role le role nécessaire pour accéder à la page - default : ROLE_AUTHOR
 * @param string $redirectLogin chemin relatif de la page de redirection si erreur accès - default : login.php
 * @return void
 */
function userIsConnected($role= 'ROLE_AUTHOR',$redirectLogin= 'login.php')
{
    $autho = false;

    //echo $_SESSION['user']['role'];
    //exit();
    /** Gestion des rôles 2 rôle pour le moment c'est assez simple, si plus de rôle on pourra gérer une hiérarchie de rôles !!
     * Si le rôle est le même que celui de l'utilisateur alors accès ok ou si le rôle utilisateur est Admin alors accès ok !
     */
    if($role == $_SESSION['user']['role'] || $_SESSION['user']['role']=='ROLE_ADMIN') 
         $autho = true; //access granted


    //Redirection vers le login si user n'a pas le droit ou n'est pas connect !
    if(!isset($_SESSION['connected']) || $_SESSION['connected'] != true || $autho == false)
    {
        addFlashBag('Vous n\'avez pas accès à cette ressource, vous devez vous connecter','danger');
        header('Location:'.$redirectLogin);
        exit();
    }

    //header('HTTP/1.0 403 Forbidden');
    
}


/** Function récursive (qui s'appelle elle même) permettant de trier le tableau des catégories
 * @param array $categories le tableau (jeu d'enregistrement) des catégories
 * @param mixed $parent l'id du parent s'il existe ou null
 */
function orderCategories($categories,$parent=null)
{
    $tree = array();

    foreach($categories as $index=>$categorie)
    {
        //var_dump($categorie['c_parent'].' '.$parent);
        if($categorie['c_parent']==$parent)
        {
            $childrens = orderCategories($categories,$categorie['c_id']);
            if(count($childrens)>0)
                $categorie['childrens'] = $childrens;
            //var_dump($categorie);
            $tree[] = $categorie;
        }
        
    }

    return $tree;
}

/** Function récursive (qui s'appelle elle même) permettant de trier le tableau des catégories
 * Cette fonction de créée pas de sous tableau mais donne un niveau de hérarchie et ordonne le tableau
 * @param array $categories le tableau (jeu d'enregistrement) des catégories
 * @param mixed $parent l'id du parent s'il existe ou null
 * @param mixed $level le niveau de hiérarchie
 */
function orderCategoriesLevel($categories,$parent=null,$level=0)
{
    $tree = array();
    foreach($categories as $index=>$categorie)
    {
        if($categorie['c_parent']==$parent)
        {
            $categorie['level'] = $level;
            $tree[] = $categorie;
            $childrens = orderCategoriesLevel($categories,$categorie['c_id'],$level+1);
            
            if(count($childrens)> 0)
                $tree = array_merge($tree,$childrens);
        }
    }
    return $tree;
}

/** Function récursive (qui s'appelle elle même) permettant d'afficher un tableau 
 * hiérarchisée à nombre infini d'enfants et sous enfants
 * Exceptionnellement nous ne pouvons faire l'affichage dans la vue et utilisons donc une fonction pour générer notre liste
 * @param array $categories le tableau (jeu d'enregistrement) des catégories
 */
function displayListeCategorie($categories)
{
    $html = '<ul class="list-group">';
    foreach($categories as $category)
    {
        if($category['articles'] > 0)
            $classBadge = 'badge-success';
        else
            $classBadge = 'badge-light';
        
        $html.= '<li class="list-group-item">'.$category['c_title'].' <a href="editCategory.php?id='.$category['c_id'].'"><i class="icon-edit"></i></a> 
         <a href="delCategory.php?id='.$category['c_id'].'" data-toggle="modal" data-target="#delete"><i class="icon-trash"></i></a>
        <span class="badge badge-pill '.$classBadge.'">'.$category['articles'].' article(s)</span>';
        if(isset($category['childrens']))
            $html.= displayListeCategorie($category['childrens']);
        $html.= '</li>';
    }
    $html.= '</ul>';

    return $html;
}
