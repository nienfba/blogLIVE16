<?php


class Application
{

    public static function run()
    {
        $controller = 'HomeController';
        $action = 'dashboard';

        if(!empty($_GET['controller']))
            $controller = $_GET['controller'].'Controller';

        if (!empty($_GET['action']))
            $action = $_GET['action'];

        $appController = new $controller();

        if($action != '')
            $appController->$action();
    }
}