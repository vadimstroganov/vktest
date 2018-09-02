<?php

include MODELS_PATH . 'item.php';
include UPLOADERS_PATH . 'image_uploader.php';

function index_action() {
  $offset      = $_GET['offset'];
  $sort_column = $_GET['sort_column'];
  $sort_type   = $_GET['sort_type'];

  $allow_sort_columns  = ['id', 'cost'];
  $allow_sort_types    = ['asc', 'desc'];
  $default_sort_column = 'id';
  $default_sort_type   = 'asc';

  // проверка на корректность выбранной колонки для сортировки
  $sort_column = strtolower($sort_column);
  if (isset($sort_column)) {
    $sort_column = in_array($sort_column, $allow_sort_columns) ? $sort_column : $default_sort_column;
  } else {
    $sort_column = $default_sort_column;
  }

  // проверка на корректность типа выбранной сортировки
  $sort_type = strtolower($sort_type);
  if (isset($sort_type)) {
    $sort_type = in_array($sort_type, $allow_sort_types) ? $sort_type : $default_sort_type;
  } else {
    $sort_type = $default_sort_type;
  }

  // значения по умолчанию для limit и offset
  $limit  = 50;
  $offset = empty($offset) ? 0 : $offset;

  $items = items_get($limit, $offset, $sort_column, $sort_type);
  echo render('items/index', [ 'items' => $items ]);
}

function create_action() {
  $image          = $_FILES['image'];
  $verified_image = isset($image) ? verify_image($image) : false;
  $image_name     = $verified_image !== false ? upload_image($verified_image) : false;

  $name        = $_POST['name'];
  $description = $_POST['description'];
  $cost        = $_POST['cost'];
  $image       = $image_name !== false ? $image_name : null;

  $item = item_create($name, $cost, $description, $image);

  if (!empty($item['id'])) {
    echo render('items/show', [ 'item' => $item ]);
  } else {
    if (isset($image)) destroy_image($image);
    render_bad_request();
  }
}

function update_action() {
  $id          = $_POST['id'];
  $name        = $_POST['name'];
  $description = $_POST['description'];
  $cost        = $_POST['cost'];

  if (empty($id)) {
    render_bad_request();
    die();
  }

  $item = item_update($id, $name, $cost, $description);

  if (!empty($item['id'])) {
    echo render('items/show', [ 'item' => $item ]);
  } else {
    render_bad_request();
  }
}

function destroy_action() {
  $id = $_POST['id'];

  if (empty($id)) {
    render_bad_request();
    die();
  }

  item_destroy($id) ? render_ok() : render_bad_request();
}
