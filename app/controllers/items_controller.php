<?php

include MODELS_PATH . 'item.php';

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
