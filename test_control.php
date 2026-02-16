<?php
// Test tanpa session requirement
$file = 'data.json';
$data = json_decode(file_get_contents($file), true);

// Test toggle L1
$dev = 'L1';
$act = 1; // Turn on

if (array_key_exists($dev, $data)) {
    $data[$dev] = $act;
    file_put_contents($file, json_encode($data));
    echo json_encode(['success'=>true, 'message'=> 'L1 is now: ' . $act]);
} else {
    echo json_encode(['success'=>false, 'message'=> 'Device not found']);
}
?>
