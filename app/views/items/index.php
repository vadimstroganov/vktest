<?php

$formatted_items = [];

foreach ($items as $item) {
  $buf = [
    'id'          => (int) $item['id'],
    'name'        => $item['name'],
    'description' => $item['description'],
    'cost'        => (int) $item['cost'],
    'image'       => image_get_public_link($item['image'])
  ];

  array_push($formatted_items, $buf);
}

echo json_encode([
  'status' => 'ok',
  'items' => $formatted_items,
  'pagination' => [
    'current_page' => $current_page,
    'total_pages' => $total_pages
  ]
]);
