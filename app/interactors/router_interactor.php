<?php

function open_route($route) {
  include CONTROLLERS_PATH . $route['controller'] . '_controller.php';
  call_user_func($route['action'] . '_action');
}

function open_url($url) {
  if (!empty(ROUTES[$url])) {
    open_route(ROUTES[$url]);
  } else {
    $route = [ 'controller' => 'errors', 'action' => 'not_found' ];
    open_route($route);
  }
}
