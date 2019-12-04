<?php

class UserModel
{
    /** 
     * @var integer MIN_FIRSTNAME_LENGTH Constante taille minimum inclue du nom d'utilisateur 
     */
    const MIN_FIRSTNAME_LENGTH = 5;
    /** 
     * @var integer MAX_FIRSTNAME_LENGTH Constante taille maximum inclue du nom d'utilisateur 
     */
    const MAX_FIRSTNAME_LENGTH = 10;

     /**
     * @var integer id de l'utilisateur
     */
    protected $id;

     /**
     * @var string firstname de l'utilisateur
     */
    protected $firstname;

     /**
     * @var string lastname de l'utilisateur
     */
    protected $lastname;
    
    /**
     * @var string de l'utilisateur
     */
    protected $email;
    
    /**
     * @var boolean role de l'utilisateur
     */
    protected $role;

    /**
     * @var string passwordde l'utilisateur
     */
    protected $password;

     /**
     * @var boolean validité de l'utilisateur
     */
    protected $valid;

    /**
     * @var PDO connexion à la base de donnée
     */
    protected $bdd;

    /**
     * @var Array collection d'objet Article - Tous les articles de l'auteur
     */
    protected $articles;

    /** Constructeur
     * @param interger $id id de l'utilisateur optionnel
     */
    public function __construct($id = null)
    {
        $this->id = $id;

        $this->bdd = new PDO(DB_DSN,DB_USER,DB_PASS);
        //On dit à PDO de nous envoyer une exception s'il n'arrive pas à se connecter ou s'il rencontre une erreur
        $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if($this->id != null)
           $this->load(); 
    }

    /** Charge un utilisateur en base à partir de son id 
     * @param void
     * @return void
     */
    public function load()
    {
        /** On recherche l'article dans la base de données */
        $sth = $this->bdd->prepare('SELECT * FROM '.DB_PREFIXE.'user WHERE u_id = :id');
        $sth->bindValue('id',$this->id,PDO::PARAM_INT);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        
        //$authorId = $article['a_author']; on ne met pas à jour l'auteur initial !
        $this->firstname = $user['u_firstname'];
        $this->lastname = $user['u_lastname'];
        $this->email = $user['u_email'];
        $this->valid = $user['u_valide'];
        $this->password = $user['u_password'];
        $this->role  = $user['u_role'];
    }

    /** Charge tous les articles d'un utilisateur
     * @param void
     * @return void
     */
    public function loadArticles()
    {
        $sth = $this->bdd->prepare('SELECT a_id FROM '.DB_PREFIXE.'article WHERE a_author = :id');
        $sth->bindValue('id',$this->id,PDO::PARAM_INT);
        $sth->execute();
        $articles = $sth->fetchAll(PDO::FETCH_ASSOC);

        foreach($articles as $article)
            $this->articles[] = new Article($article['a_id']);

    }

    /** Charge un utilisateurs par email
     * @param string $email email de l'utilisateur
     * @return void
     */
    public function loadByEmail($email)
    {
        /** On recherche l'article dans la base de données */
        $sth = $this->bdd->prepare('SELECT * FROM '.DB_PREFIXE.'user WHERE u_email = :email');
        $sth->bindValue('email',$email,PDO::PARAM_STR);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        
        //$authorId = $article['a_author']; on ne met pas à jour l'auteur initial !
        $this->id = $user['u_id'];
        $this->firstname = $user['u_firstname'];
        $this->lastname = $user['u_lastname'];
        $this->email = $user['u_email'];
        $this->valid = $user['u_valide'];
        $this->password = $user['u_password'];
        $this->role  = $user['u_role'];
    }


    public function save()
    {
        if($this->id == null)
        {
            //Préparation requête
            $sth = $this->bdd->prepare('INSERT INTO '.DB_PREFIXE.'user 
            (u_id,u_firstname,u_lastname,u_email, u_password,u_valide,u_role)
            VALUES (NULL,:firstname,:lastname,:email, :password,:valide,:role)');

            //Liage (bind) des valeurs
            $sth->bindValue('firstname', $this->firstname, PDO::PARAM_STR);
            $sth->bindValue('lastname', $this->lastname, PDO::PARAM_STR);
            $sth->bindValue('email', $this->email, PDO::PARAM_STR);
            $sth->bindValue('password', $this->password, PDO::PARAM_STR);
            $sth->bindValue('valide', $this->valid, PDO::PARAM_INT);
            $sth->bindValue('role', $this->role, PDO::PARAM_STR);
            $sth->execute();

        }
        else
        {
              //$passwordUser = password_hash($passwordUser,PASSWORD_DEFAULT);
            $sth = $this->bdd->prepare('UPDATE '.DB_PREFIXE.'user SET
            u_firstname = :firstname,u_lastname=:lastname,u_email=:email, u_valide=:valide,u_role=:role 
            WHERE u_id=:id');
            $sth->bindValue('id',$this->id,PDO::PARAM_INT);
            $sth->bindValue('firstname',$this->firstname,PDO::PARAM_STR);
            $sth->bindValue('lastname',$this->lastname,PDO::PARAM_STR);
            $sth->bindValue('email',$this->email,PDO::PARAM_STR);
            $sth->bindValue('valide',$this->valid ,PDO::PARAM_BOOL);
            $sth->bindValue('role',$this->role,PDO::PARAM_STR);
            $sth->execute();
            
        }

    }

    public function delete()
    {
        $sth = $this->bdd->prepare('DELETE FROM '.DB_PREFIXE.'user WHERE u_id = :id');
        $sth->bindValue('id',$this->id,PDO::PARAM_INT);
        $sth->execute();
    }

    /**
     * Get id de l'utilisateur
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id de l'utilisateur
     *
     * @param  integer  $id  id de l'utilisateur
     *
     * @return  self
     */ 
    public function setId($id):UserModel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get firstname de l'utilisateur
     *
     * @return  string
     */ 
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set firstname de l'utilisateur
     *
     * @param  string  $firstname  firstname de l'utilisateur
     *
     * @return  self
     */ 
    public function setFirstname(string $firstname):UserModel
    {
        if(count($firstname) >= MIN_FIRSTNAME_LENGTH || count($firstname) <= MAX_FIRSTNAME_LENGTH)
            throw new DomainException('Le nom d\'utilisateur n\'est pas correct');

        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get lastname de l'utilisateur
     *
     * @return  string
     */ 
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set lastname de l'utilisateur
     *
     * @param  string  $lastname  lastname de l'utilisateur
     *
     * @return  self
     */ 
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get de l'utilisateur
     *
     * @return  string
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set de l'utilisateur
     *
     * @param  string  $email  de l'utilisateur
     *
     * @return  self
     */ 
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get role de l'utilisateur
     *
     * @return  boolean
     */ 
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role de l'utilisateur
     *
     * @param  boolean  $role  role de l'utilisateur
     *
     * @return  self
     */ 
    public function setRole(boolean $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get passwordde l'utilisateur
     *
     * @return  string
     */ 
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set passwordde l'utilisateur
     *
     * @param  string  $password  passwordde l'utilisateur
     *
     * @return  self
     */ 
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get validité de l'utilisateur
     *
     * @return  boolean
     */ 
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * Set validité de l'utilisateur
     *
     * @param  boolean  $valid  validité de l'utilisateur
     *
     * @return  self
     */ 
    public function setValid(boolean $valid)
    {
        $this->valid = $valid;

        return $this;
    }
} 

