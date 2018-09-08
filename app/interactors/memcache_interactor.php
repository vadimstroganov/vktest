<?php

/**
 * Создание соединения с Memcached
 *
 * @return memcache|bool
 */
function create_memcache_connection() {
  $host = $_ENV['MEMCACHED_HOST'] ?: '127.0.0.1';
  $port = $_ENV['MEMCACHED_PORT'] ?: '11211';

  $connection = memcache_connect($host, $port);

  // проверка соединения
  if ($connection === false) {
    echo "Не удалось подключиться к Memcached";
    exit();
  }

  return $connection;
}

/**
 * Закрытие соединения с Memcached
 *
 * @param memcache $connection Ссылка на соединение с Memcached
 *
 * @return void
 */
function close_memcache_connection($connection) {
  memcache_close($connection);
}

/**
 * Получение значения по ключу из Memcached
 *
 * @param string $key Ключ, с которым связано значение элемента
 *
 * @return mixed
 */
function mc_get($key)
{
  $connection = create_memcache_connection();
  $res = memcache_get($connection, $key);
  close_memcache_connection($connection);

  return $res;
}

/**
 * Запись значения по ключу в Memcached
 *
 * @param string $key Ключ, с которым будет связано значение элемента
 * @param mixed $var Переменная для сохранения
 * @param int $expire Переменная для сохранения
 * @param int $flag Флаг использования сжатия
 *
 * @return mixed
 */
function mc_set($key, $var, $expire = 0, $flag = 0)
{
  $connection = create_memcache_connection();
  $res = memcache_set($connection, $key, $var, $flag, $expire);
  close_memcache_connection($connection);

  return $res;
}
