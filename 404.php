<?php
$vue = '404';
$title="Erreur 404 - Page introuvable !";
$bgImage = 'img/404.jpg';
$subTitle = 'oU aLORS tU eS pERDU !';

header("HTTP/1.0 404 Not Found");

include('tpl/layout.phtml');

