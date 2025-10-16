<?php
header('Content-Type: application/json');
$post = $_POST;
$room = trim($post['room'] ?? '');
$user = trim($post['user'] ?? '');
$file = __DIR__ . '/../data/rooms.json';
if ($room === '' || $user === '') { echo json_encode(['success'=>false,'error'=>'missing']); exit; }

$fp = fopen($file, 'c+');
if (!$fp) { echo json_encode(['success'=>false,'error'=>'open']); exit; }
flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$data = $contents ? json_decode($contents, true) : [];
if (!isset($data[$room])) { flock($fp, LOCK_UN); fclose($fp); echo json_encode(['success'=>false,'error'=>'not_found']); exit; }
$host = $data[$room]['host'] ?? '';
if ($host !== $user) { flock($fp, LOCK_UN); fclose($fp); echo json_encode(['success'=>false,'error'=>'not_host']); exit; }

// remove room
unset($data[$room]);
ftruncate($fp,0); rewind($fp);
fwrite($fp, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
fflush($fp); flock($fp, LOCK_UN); fclose($fp);

// remove members for the room
$membersFile = __DIR__ . '/../data/members.json';
if (file_exists($membersFile)) {
  $mfp = fopen($membersFile,'c+'); if ($mfp) {
    flock($mfp, LOCK_EX);
    $mcontents = stream_get_contents($mfp);
    $mdata = $mcontents ? json_decode($mcontents, true) : [];
    unset($mdata[$room]);
    ftruncate($mfp,0); rewind($mfp); fwrite($mfp, json_encode($mdata, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    fflush($mfp); flock($mfp, LOCK_UN); fclose($mfp);
  }
}

echo json_encode(['success'=>true]);
exit;
?>