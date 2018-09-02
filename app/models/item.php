<?php

/**
 * Получение списка товаров из БД
 *
 * @param string $sort_column Название колонки
 * @param string $sort_type Тип сортировки (SQL)
 * @param integer $offset Отступ
 *
 * @return array
 */
function items_get($limit, $offset, $sort_column, $sort_type) {
  $connection = create_db_connection();

  // экранизация параметров
  $offset      = mysqli_real_escape_string($connection, $offset);
  $sort_column = mysqli_real_escape_string($connection, $sort_column);
  $sort_type   = mysqli_real_escape_string($connection, $sort_type);

  $sql    = "SELECT * FROM items ORDER BY {$sort_column} {$sort_type} LIMIT {$offset}, {$limit}";
  $result = mysqli_query($connection, $sql);

  close_db_connection($connection);

  $items = [];
  while ($row = mysqli_fetch_assoc($result)) {
    array_push($items, $row);
  }

  return $items;
}

/**
 * Добавление товара в БД
 *
 * @param string $name
 * @param integer $cost
 * @param string $description
 * @param string $image
 *
 * @return array
 */
function item_create($name, $cost, $description = null, $image = null) {
  $connection = create_db_connection();

  // экранизация параметров
  $name        = mysqli_real_escape_string($connection, $name);
  $cost        = mysqli_real_escape_string($connection, $cost);
  $description = mysqli_real_escape_string($connection, $description);
  $image       = mysqli_real_escape_string($connection, $image);

  $sql = "INSERT INTO items (name, description, cost, image) VALUES ('$name', '$description', '$cost', '$image')";
  mysqli_query($connection, $sql);
  $insert_id = mysqli_insert_id($connection);

  close_db_connection($connection);

  if (empty($insert_id)) {
    return [];
  }

  return [
    'id'          => $insert_id,
    'name'        => $name,
    'description' => $description,
    'image'       => $image
  ];
}

function item_update($id, $name, $cost, $description = null, $image = null) {
  $connection = create_db_connection();

  // экранизация параметров
  $id          = mysqli_real_escape_string($connection, $id);
  $name        = mysqli_real_escape_string($connection, $name);
  $cost        = mysqli_real_escape_string($connection, $cost);
  $description = mysqli_real_escape_string($connection, $description);
  $image       = mysqli_real_escape_string($connection, $image);

  $sql = "UPDATE items SET name='$name', description='$description', cost='$cost', image='$image' WHERE id=$id";
  $bool = mysqli_query($connection, $sql);

  close_db_connection($connection);

  // возвращаем пустой массив, если запрос завершился с ошибкой
  if (!$bool) {
    return [];
  }

  return [
    'id'          => $id,
    'name'        => $name,
    'description' => $description,
    'image'       => $image
  ];
}

/**
 * Удаление товара из БД
 *
 * @param integer $id
 *
 * @return bool
 */
function item_destroy($id) {
  $connection = create_db_connection();

  $id   = mysqli_real_escape_string($connection, $id);
  $sql  = "DELETE FROM items WHERE id=$id";
  $bool = mysqli_query($connection, $sql);

  close_db_connection($connection);

  return $bool;
}
