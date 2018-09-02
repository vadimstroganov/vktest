<?php

echo json_encode([
  'status' => 'ok',
  'item' => [
    'id'          => (int) $item['id'],
    'name'        => $item['name'],
    'description' => $item['description'],
    'cost'        => (int) $item['cost'],
    'image'       => $item['image']
  ]
]);
