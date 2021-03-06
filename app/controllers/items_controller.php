<?php

include MODELS_PATH . 'item.php';
include UPLOADERS_PATH . 'image_uploader.php';

function index_action() {
  $page        = (int) $_GET['page'] ?: 1;
  $sort_column = $_GET['sort_column'];
  $sort_type   = $_GET['sort_type'];

  $allow_sort_columns  = ['id', 'cost'];
  $allow_sort_types    = ['asc', 'desc'];
  $default_sort_column = 'id';
  $default_sort_type   = 'asc';

  // Получаем информацию о кол-ве страниц с товарами
  $total_pages = items_get_total_quantity();

  // Если запрошенная страница, превышает максимальное кол-во страниц,
  // сразу же отправляем пустой ответ, минуя дальнейшие запросы в кэш и БД
  if ($page > $total_pages) {
    echo render('items/index', [ 'items' => [], 'current_page' => $page, 'total_pages' => $total_pages ]);
    die();
  }

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

  $items = items_get($page, $sort_column, $sort_type);
  echo render('items/index', [ 'items' => $items, 'current_page' => $page, 'total_pages' => $total_pages ]);
}

function show_action() {
  $id = $_GET['id'];

  // вызываем ошибку, если не передан параметр id
  if (empty($id)) render_bad_request();

  $item = item_get($id);

  // вызываем ошибку, если товара с таким id не существует
  if (empty($item)) render_bad_request();

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
  if (empty($id)) render_bad_request();

  $item = item_get($id);

  // вызываем ошибку, если товара с таким id не существует
  if (empty($item)) render_bad_request();

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

  // вызываем ошибку, если не передан параметр id
  if (empty($id)) render_bad_request();

  $item = item_get($id);

  // вызываем ошибку, если товара с таким id не существует
  if (empty($item)) render_bad_request();

  $result = item_destroy($id);

  if ($result) {
    // удаляем изображение, если оно существует
    if (!empty($item['image'])) destroy_image($item['image']);

    render_ok();
  } else {
    render_bad_request();
  }
}
