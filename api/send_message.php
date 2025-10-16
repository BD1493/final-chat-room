<?php
header('Content-Type: application/json');
$room = trim($_GET['room'] ?? '');
$channel = trim($_GET['channel'] ?? 'general');
$user = trim($_GET['user'] ?? 'unknown');
$text = trim($_GET['text'] ?? '');

if ($room === '' || $text === '') { echo json_encode(['success'=>false,'error'=>'missing']); exit; }

$file = __DIR__ . '/../data/rooms.json';
$fp = fopen($file, 'c+');
if (!$fp) { echo json_encode(['success'=>false,'error'=>'open']); exit; }
flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$data = $contents ? json_decode($contents, true) : [];
if (!is_array($data)) $data = [];

if (!isset($data[$room])) {
  // create room skeleton if missing
  $data[$room] = ['subject'=>'','host'=>'','channels'=>['general'=>[]]];
}
if (!isset($data[$room]['channels'][$channel])) {
  $data[$room]['channels'][$channel] = [];
}

$data[$room]['channels'][$channel][] = ['user'=>$user, 'text'=>$text, 'time'=>time()];

ftruncate($fp,0); rewind($fp);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['success'=>true]);
exit;   
?>