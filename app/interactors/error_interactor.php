<?php

function render_not_found() {
  http_response_code(404);
  echo json_encode([ 'status' => 'error', 'message' => 'Not found' ]);
}

function render_bad_request() {
  http_response_code(400);
  echo json_encode([ 'status' => 'error', 'message' => 'Bad request' ]);
}
