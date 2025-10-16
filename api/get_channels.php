<?php
header('Content-Type: application/json');
$room = trim($_GET['room'] ?? '');
$file = __DIR__ . '/../data/rooms.json';
$contents = file_exists($file) ? file_get_contents($file) : '';
$data = $contents ? json_decode($contents, true) : [];
$channels = [];
if (isset($data[$room]['channels'])) $channels = array_keys($data[$room]['channels']);
if (empty($channels)) $channels = ['general'];
echo json_encode($channels);
exit;  
?>