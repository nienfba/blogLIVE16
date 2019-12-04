<?php
session_start();
/**On inclu d'abord le fichier de configuration */
include('../config/config.php');
/**On inclu ensuite nos librairies dont le programme a besoin */
require_once('../lib/app.lib.php');

spl_autoload_register('autoloadApplicationClasse');

function autoloadApplicationClasse($class)
{
    if(file_exists('../lib/' . $class . '.php'))
        require_once('../lib/'.$class.'.php');
    elseif (file_exists('../lib/controllers/' . $class . '.php'))
        require_once('../lib/controllers/' . $class . '.php');
    elseif (file_exists('../lib/models/' . $class . '.php'))
        require_once('../lib/models/' . $class . '.php');
}

// >TODO : voir comment modifier les droits pour certains controller et certaines méthodes ?
userIsConnected();

//Lance l'application
Application::run();