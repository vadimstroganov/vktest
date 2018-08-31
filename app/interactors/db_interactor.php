<?php

/**
 * Создание соединения с базой данных
 *
 * @return mysqli
 */
function create_db_connection() {
  $host = $_ENV['DB_HOST'];
  $db   = $_ENV['DB_NAME'];
  $user = $_ENV['DB_USER'];
  $pass = $_ENV['DB_PASS'];

  $connection = mysqli_connect($host, $user, $pass, $db);

  // проверка соединения
  if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
  }

  return $connection;
}

/**
 * Закрытие соединения с базой данных
 *
 * @param mysqli $connection Ссылка на соединение с базой данных
 *
 * @return void
 */
function close_db_connection($connection) {
  mysqli_close($connection);
}
