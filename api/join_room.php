<?php
// join_room.php
header('Content-Type: application/json');

$code = $_GET['code'] ?? '';
$code = trim($code);

if ($code === '') {
    echo json_encode(['success' => false, 'error' => 'Missing room code']);
    exit;
}

$dataFile = __DIR__ . "/../data/rooms.json";
$fp = fopen($dataFile, 'r');

if (!$fp) {
    echo json_encode(['success' => false, 'error' => 'Unable to open data file']);
    exit;
}

flock($fp, LOCK_SH);
$contents = stream_get_contents($fp);
$data = $contents ? json_decode($contents, true) : [];
flock($fp, LOCK_UN);
fclose($fp);

if (isset($data[$code])) {
    echo json_encode(['success' => true, 'subject' => $data[$code]['subject'] ?? '']);
} else {
    echo json_encode(['success' => false, 'error' => 'Room not found']);
}
exit;
?>
