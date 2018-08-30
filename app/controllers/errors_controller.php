<?php

function not_found_action() {
  http_response_code(404);
  echo render('errors/not_found');
}
