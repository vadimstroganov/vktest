<?php

$formatted_items = [];

foreach ($items as $item) {
  $buf = [
    'id'          => (int) $item['id'],
    'name'        => $item['name'],
    'description' => $item['description'],
    'cost'        => (int) $item['cost'],
    'image'       => $item['image']
  ];

  array_push($formatted_items, $buf);
}

echo json_encode([
  'status' => 'ok',
  'items' => $formatted_items
]);
