<?php
session_start();
header('Content-Type: application/json');

// Keamanan: Cek Login (support both old dan new system)
if (!isset($_SESSION['sudah_login']) && !isset($_SESSION['username'])) {
    http_response_code(403);
    die(json_encode(['success'=>false, 'message'=>'Login Required']));
}

$file = 'data.json';
if (!file_exists($file)) {
    http_response_code(500);
    die(json_encode(['success'=>false, 'message'=>'Data file not found']));
}

$data = json_decode(file_get_contents($file), true);
$dev = isset($_POST['device']) ? $_POST['device'] : null; 
$act = isset($_POST['action']) ? intval($_POST['action']) : null;

if (!$dev || $act === null) {
    http_response_code(400);
    die(json_encode(['success'=>false, 'message'=>'Device or action not provided']));
}

if (array_key_exists($dev, $data)) {
    $data[$dev] = $act;
    if (file_put_contents($file, json_encode($data))) {
        echo json_encode(['success'=>true, 'message'=>'Device updated', 'device'=>$dev, 'action'=>$act]);
    } else {
        http_response_code(500);
        echo json_encode(['success'=>false, 'message'=>'Failed to update device']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success'=>false, 'message'=>'Device not found']);
}
?>
