<?php
header('Content-Type: application/json');
$room = trim($_GET['room'] ?? '');
$user = trim($_GET['user'] ?? '');
$file = __DIR__ . '/../data/members.json';

$fp = fopen($file, 'c+');
if (!$fp) { echo json_encode(['success'=>false]); exit; }
flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$data = $contents ? json_decode($contents, true) : [];
if (!is_array($data)) $data = [];

// init room
if (!isset($data[$room])) $data[$room] = [];
if ($user !== '') $data[$room][$user] = time();

// cleanup inactive (30s)
foreach ($data[$room] as $u => $t) {
  if (time() - $t > 30) unset($data[$room][$u]);
}

ftruncate($fp,0); rewind($fp);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
fflush($fp); flock($fp, LOCK_UN); fclose($fp);
echo json_encode(['success'=>true]);
if (isset($data[$room])) {
  echo json_encode(['members' => array_keys($data[$room])]);
} else {
  echo json_encode(['members' => []]);
} exit;
?>