<?php

echo json_encode([
  'status' => 'ok',
  'item' => [
    'id'          => $item['id'],
    'name'        => $item['name'],
    'description' => $item['description'],
    'cost'        => $item['cost'],
    'image'       => $item['image']
  ]
]);
