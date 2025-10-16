<?php
header('Content-Type: application/json');
$room = trim($_GET['room'] ?? '');
$channel = trim($_GET['channel'] ?? 'general');
$file = __DIR__ . '/../data/rooms.json';
$data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

$messages = $data[$room]['channels'][$channel] ?? [];
// ensure it's an array
if (!is_array($messages)) $messages = [];
echo json_encode($messages);
exit;  
?>  