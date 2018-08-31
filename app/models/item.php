<?php

/**
 * Получение списка товаров из БД
 *
 * @param string $sort_column Название колонки
 * @param string $sort_type Тип сортировки (SQL)
 * @param integer $offset Отступ
 *
 * @return array Товары
 */
function items_get($limit, $offset, $sort_column, $sort_type) {
  $connection = create_db_connection();

  // экранизация полученных параметров
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
