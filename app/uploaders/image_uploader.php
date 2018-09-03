<?php

define('UPLOADS_DIR', DOCROOT . 'uploads/');
define('UPLOADS_DIR_RELATIVE', '/uploads/');

/**
 * Проверка, является ли загруженный файл изображением
 *
 * @param string $temp_file
 *
 * @return array|bool
 */
function verify_image($temp_file) {
  $verified_image = getimagesize($temp_file['tmp_name']);

  // если было передано не изображение, то процесс завершится неудачей
  if (!$verified_image) {
    return false;
  }

  // проверяем вхождение mime_type
  $pattern = "#^(image/)[^\s\n<]+$#i";
  if (!preg_match($pattern, $verified_image['mime'])) {
     return false;
  }

  $verified_image['tmp_name']  = $temp_file['tmp_name'];
  $verified_image['extension'] = image_get_extension($verified_image['mime']);

  return $verified_image;
}

/**
 * Сохранение изображения в файловую систему
 *
 * @param $file
 *
 * @return bool|string
 */
function upload_image($file) {
  $image_name = generate_image_name() . $file['extension'];
  $save_path  = UPLOADS_DIR . $image_name;

  if (move_uploaded_file($file['tmp_name'], $save_path)) return $image_name;
  return false;
}

/**
 * Удаление загруженного изображения из файловой системы
 *
 * @param string $image_name
 *
 * @return bool
 */
function destroy_image($image_name) {
  return unlink(UPLOADS_DIR . $image_name);
}

/**
 * Генерация уникального идентификатора для изображения
 *
 * @return string
 */
function generate_image_name() {
  return md5(uniqid(rand(), true));
}

/**
 * Получение расширения изображения (с точкой)
 *
 * @param string $mime
 *
 * @return bool|string
 */
function image_get_extension($mime) {
  $image_mimes = '{"gif":["image\/gif"], "jpg":["image\/jpeg", "image\/pjpeg"], "png":["image\/png", "image\/x-png"], "svg":["image\/svg+xml"], "tiff":["image\/tiff"]}';
  $image_mimes = json_decode($image_mimes, true);

  foreach ($image_mimes as $key => $value) {
    if (array_search($mime, $value) !== false) return '.' . $key;
  }

  return false;
}

function image_get_public_link($image_name) {
  if (empty($image_name)) return null;
  return $_ENV['UPLOADS_HOST'] . UPLOADS_DIR_RELATIVE . $image_name;
}
