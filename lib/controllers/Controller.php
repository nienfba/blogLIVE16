<?php


class Controller
{

    /**
     * @var string Vue pour la sortie dans le layout
     */
    private $view;

    /**
     * @var string titre de la page
     */
    private $title;

    /** 
     * @var string l'alias de la page sélectionnée dans le menu
     */
    private $menuSelected;


    public function render(array $viewVars)
    {
        extract($viewVars);
        $title = $this->title;
        $view = $this->view;
        $menuSelected = $this->menuSelected;

        include(TPL_ADMIN_DIR.'layout.phtml');
    }


    /**
     * Get vue pour la sortie dans le layout
     *
     * @return  string
     */ 
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set vue pour la sortie dans le layout
     *
     * @param  string  $view  Vue pour la sortie dans le layout
     *
     * @return  self
     */ 
    public function setView(string $view)
    {
        if(!file_exists(TPL_ADMIN_DIR.$view))
            throw new ViewException("La vue $view n'existe pas !");
        
        $this->view = $view;

        return $this;
    }

    /**
     * Get titre de la page
     *
     * @return  string
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set titre de la page
     *
     * @param  string  $title  titre de la page
     *
     * @return  self
     */ 
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get l'alias de la page sélectionnée dans le menu
     *
     * @return  string
     */ 
    public function getMenuSelected()
    {
        return $this->menuSelected;
    }

    /**
     * Set l'alias de la page sélectionnée dans le menu
     *
     * @param  string  $menuSelected  l'alias de la page sélectionnée dans le menu
     *
     * @return  self
     */ 
    public function setMenuSelected(string $menuSelected)
    {
        $this->menuSelected = $menuSelected;

        return $this;
    }
}