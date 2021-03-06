<?php

define('ITEMS_DEFAULT_LIMIT', 50);

/**
 * Получение списка товаров из БД
 *
 * @param string $sort_column Название колонки
 * @param string $sort_type Тип сортировки (SQL)
 * @param integer $offset Отступ
 *
 * @return array
 */
function items_get($page, $sort_column, $sort_type) {
  $md5        = md5("{$page}_{$sort_column}_{$sort_type}");
  $md5_prefix = 'items_get_';
  $md5_key    = $md5_prefix . $md5;

  $items = mc_get($md5_key) ?: [];

  // Если обнаружили данные в кэше, то возвращаем их
  if (!empty($items)) return $items;

  $connection = create_db_connection();

  // Экранизация параметров
  $sort_column = mysqli_real_escape_string($connection, $sort_column);
  $sort_type   = mysqli_real_escape_string($connection, $sort_type);

  // Выставляем LIMIT по умочанию
  $limit = ITEMS_DEFAULT_LIMIT;

  // Рассчитываем offset
  // Так как у клиента параметры нумерации начинаются не с 0, а с 1, то необходимо
  // вычесть единицу из параметра $page (у страницы 1, offset = 0, страницы 2 = 1 * $limit и тд.)
  $offset = ($page - 1) * $limit;

  $sql    = "SELECT * FROM items
             JOIN (SELECT id FROM items ORDER BY {$sort_column} {$sort_type} LIMIT {$offset}, {$limit}) AS subquery
             ON subquery.id = items.id";

  $result = mysqli_query($connection, $sql);

  close_db_connection($connection);

  while ($row = mysqli_fetch_assoc($result)) {
    array_push($items, $row);
  }

  // Записываем результат выполнения SQL запроса в кэш
  mc_set($md5_key, $items, 60 * 5); // expire в секундах

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
  $prefix = 'item_';
  $key    = $prefix . $id;
  $item   = mc_get($key);

  // Если обнаружили данные в кэше, то возвращаем их
  if (!empty($item)) return $item;

  $connection = create_db_connection();

  $id = mysqli_real_escape_string($connection, $id);

  $sql        = "SELECT * FROM items WHERE id={$id} LIMIT 1";
  $raw_result = mysqli_query($connection, $sql);
  $result     = mysqli_fetch_assoc($raw_result);

  close_db_connection($connection);

  // Записываем результат выполнения SQL запроса в кэш
  mc_set($key, $result, 60 * 5); // expire в секундах

  return $result;
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

  // Сброс кэша
  mc_flush();

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

  // Сброс кэша
  mc_flush();

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

  // Сброс кэша
  mc_flush();

  return $bool;
}

/**
 * Получение кол-ва существующих страниц с товарами
 *
 * @return int
 */
function items_get_total_quantity() {
  $key   = 'items_total_quantity';
  $total = mc_get($key);

  // Если обнаружили данные в кэше, то возвращаем их
  if (!empty($total)) return $total;

  $connection = create_db_connection();

  $sql    = 'SELECT COUNT(id) FROM items';
  $result = mysqli_query($connection, $sql);

  $raw_total = (int) mysqli_fetch_row($result)[0];
  $total     = ceil($raw_total / ITEMS_DEFAULT_LIMIT);

  close_db_connection($connection);

  // Записываем результат выполнения SQL запроса в кэш
  mc_set($key, $total, 60 * 5); // expire в секундах

  return $total;
}
