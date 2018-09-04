<?php

define('DOCROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
define('CONTROLLERS_PATH', DOCROOT . 'app/controllers/');
define('INTERACTORS_PATH', DOCROOT . 'app/interactors/');
define('MODELS_PATH', DOCROOT . 'app/models/');
define('VIEWS_PATH', DOCROOT . 'app/views/');
define('UPLOADERS_PATH', DOCROOT . 'app/uploaders/');

include INTERACTORS_PATH . 'router_interactor.php';
include INTERACTORS_PATH . 'view_interactor.php';
include INTERACTORS_PATH . 'db_interactor.php';
include INTERACTORS_PATH . 'error_interactor.php';

const ROUTES = [
  'GET /items'         => [ 'controller' => 'items', 'action' => 'index' ],
  'GET /items/show'    => [ 'controller' => 'items', 'action' => 'show' ],
  'POST /items/create' => [ 'controller' => 'items', 'action' => 'create' ],
  'POST /items/update' => [ 'controller' => 'items', 'action' => 'update' ],
  'POST /items/delete' => [ 'controller' => 'items', 'action' => 'destroy' ]
];

$request_method = $_SERVER['REQUEST_METHOD'];
$request_url    = strtok($_SERVER['REQUEST_URI'], '?');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

open_url("{$request_method} {$request_url}");
