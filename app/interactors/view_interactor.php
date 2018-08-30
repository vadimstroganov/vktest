<?php

function render($template, $data = []) {
  extract($data);
  ob_start();
  include VIEWS_PATH . $template . '.php';
  return $render_result = ob_get_clean();
}
