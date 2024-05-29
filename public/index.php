<?php

// Chargement automatique des classes
require dirname(__DIR__) . '/wal/autoload.php';

/*
|--------------------------------------------------------------------------
|   Chargement du router
|--------------------------------------------------------------------------
*/
use Wal\Router\Router;
new Router();
