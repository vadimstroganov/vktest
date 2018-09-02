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

function show_action() {
  $id = $_GET['id'];

  if (empty($id)) {
    render_bad_request();
    die();
  }

  $item = item_get($id);
  echo render('items/show', [ 'item' => $item ]);
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
  $id = $_POST['id'];

  // вызываем ошибку, если не передан параметр id
  if (empty($id)) {
    render_bad_request();
    die();
  }

  $item = item_get($id);

  // вызываем ошибку, если товара с таким id не существует
  if (empty($item)) {
    render_bad_request();
    die();
  }

  $image          = $_FILES['image'];
  $verified_image = isset($image)             ? verify_image($image)          : false;
  $new_image      = $verified_image !== false ? upload_image($verified_image) : false;
  $old_image      = $item['image'];

  $name        = isset($_POST['name'])        ? $_POST['name']        : $item['name'];
  $description = isset($_POST['description']) ? $_POST['description'] : $item['description'];
  $cost        = isset($_POST['cost'])        ? $_POST['cost']        : $item['cost'];
  $image       = $new_image !== false         ? $new_image            : $old_image;

  $item = item_update($id, $name, $cost, $description, $image);

  if (!empty($item['id'])) {
    // удаляем старое изображение, если появилось новое
    if ($new_image !== false && $new_image != $old_image) {
      destroy_image($old_image);
    }

    echo render('items/show', [ 'item' => $item ]);
  } else {
    // удаляем новое изображение, в случае неудачи выполнения запроса на обновление товара
    if (isset($new_image)) {
      destroy_image($new_image);
    }

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
