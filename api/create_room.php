<?php
header('Content-Type: application/json');
$subject = trim($_GET['subject'] ?? 'Private Room');
$host = trim($_GET['host'] ?? $_GET['user'] ?? '');

$code = strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));
$file = __DIR__ . '/../data/rooms.json';

// open with lock
$fp = fopen($file, 'c+');
if (!$fp) { echo json_encode(['success'=>false,'error'=>'open_failed']); exit; }
flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$data = $contents ? json_decode($contents, true) : [];
if (!is_array($data)) $data = [];

$data[$code] = [
  'subject' => $subject,
  'host' => $host,
  'channels' => ['general' => []]  // channel => array of messages
];

ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);

echo json_encode(['success'=>true,'code'=>$code]);
exit;
?>