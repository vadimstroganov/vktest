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
 * Получение конкретного товара из БД
 *
 * @param $id
 *
 * @return array|null
 */
function item_get($id) {
  $connection = create_db_connection();

  $id = mysqli_real_escape_string($connection, $id);

  $sql = "SELECT * FROM items WHERE id={$id} LIMIT 1";
  $result = mysqli_query($connection, $sql);

  close_db_connection($connection);

  return mysqli_fetch_assoc($result);
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

  $sql = "INSERT INTO items (name, description, cost, image) VALUES (?, ?, ?, ?)";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, 'ssis', $name, $description, $cost, $image);
  mysqli_stmt_execute($stmt);

  $insert_id = mysqli_stmt_insert_id($stmt);

  mysqli_stmt_close($stmt);
  close_db_connection($connection);

  if (empty($insert_id)) {
    return [];
  }

  return [
    'id'          => $insert_id,
    'name'        => $name,
    'description' => $description,
    'cost'        => $cost,
    'image'       => $image
  ];
}

/**
 * Обновление товара в БД
 *
 * @param integer $id
 * @param string $name
 * @param integer $cost
 * @param string $description
 * @param string $image
 *
 * @return array
 */
function item_update($id, $name, $cost, $description = null, $image = null) {
  $connection = create_db_connection();

  $sql = "UPDATE items SET name = ?, description = ?, cost = ?, image = ? WHERE id = ?";
  $stmt = mysqli_prepare($connection, $sql);

  mysqli_stmt_bind_param($stmt, 'ssisi', $name, $description, $cost, $image, $id);
  $bool = mysqli_stmt_execute($stmt);

  mysqli_stmt_close($stmt);
  close_db_connection($connection);

  // возвращаем пустой массив, если запрос завершился с ошибкой
  if (!$bool) {
    return [];
  }

  return [
    'id'          => $id,
    'name'        => $name,
    'description' => $description,
    'cost'        => $cost,
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
