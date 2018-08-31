<?php

define('DOCROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
define('CONTROLLERS_PATH', DOCROOT . 'app/controllers/');
define('INTERACTORS_PATH', DOCROOT . 'app/interactors/');
define('MODELS_PATH', DOCROOT . 'app/models/');
define('VIEWS_PATH', DOCROOT . 'app/views/');

include INTERACTORS_PATH . 'router_interactor.php';
include INTERACTORS_PATH . 'view_interactor.php';
include INTERACTORS_PATH . 'db_interactor.php';

const ROUTES = [
  '/items' => [ 'controller' => 'items', 'action' => 'index' ]
];

header('Content-Type: application/json');
open_url(strtok($_SERVER["REQUEST_URI"],'?'));
