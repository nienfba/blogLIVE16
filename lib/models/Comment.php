<?php
require_once('../lib/bdd.lib.php');
require_once('Model.php');

class Comment extends Model
{
    protected $table="comment";
}