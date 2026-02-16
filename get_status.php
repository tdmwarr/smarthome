<?php
header('Content-Type: application/json');

$file = 'data.json';
if (!file_exists($file)) {
    http_response_code(500);
    die(json_encode(['error'=>'Data file not found']));
}

$data = @file_get_contents($file);
if ($data === false) {
    http_response_code(500);
    die(json_encode(['error'=>'Cannot read data file']));
}

echo $data;
?>