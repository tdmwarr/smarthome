<?php
header('Content-Type: application/json');
$file = 'data.json';
$data = json_decode(file_get_contents($file), true);

// 1. ESP32 Lapor Data
if (isset($_GET['suhu'])) {
    $data['suhu'] = $_GET['suhu'];
    $data['kelembaban'] = $_GET['hum'];
    $data['last_update'] = date("H:i:s");

    // Update Lampu jika ada perintah otomatisasi dari sensor
    for ($i=1; $i<=6; $i++) {
        $k = "L".$i;
        if (isset($_GET[$k])) $data[$k] = intval($_GET[$k]);
    }
    file_put_contents($file, json_encode($data));
}

// 2. LDR Sensor Data
if (isset($_GET['ldr'])) {
    $data['ldr'] = intval($_GET['ldr']);
    $data['last_update'] = date("H:i:s");
    
    // Auto-control front light (L6) based on LDR sensor
    if (isset($data['ldr_auto_on']) && intval($data['ldr_auto_on']) === 1) {
        $ldr_threshold = isset($data['ldr_threshold']) ? intval($data['ldr_threshold']) : 400;
        if (intval($data['ldr']) < $ldr_threshold) {
            $data['L6'] = 1; // Turn on front light
        } else {
            $data['L6'] = 0; // Turn off front light
        }
    }
    file_put_contents($file, json_encode($data));
    echo json_encode($data);
    exit;
}

// 3. PIR Sensor Data
if (isset($_GET['pir'])) {
    $data['pir'] = intval($_GET['pir']);
    $data['last_update'] = date("H:i:s");
    file_put_contents($file, json_encode($data));
}

// 4. LDR Settings Update
if (isset($_GET['ldr_threshold'])) {
    $data['ldr_threshold'] = intval($_GET['ldr_threshold']);
    file_put_contents($file, json_encode($data));
}

if (isset($_GET['ldr_auto_on'])) {
    $data['ldr_auto_on'] = intval($_GET['ldr_auto_on']);
    file_put_contents($file, json_encode($data));
}

// 5. Reset Status Pintu (Setelah Servo Gerak)
if (isset($_GET['ack_pintu'])) {
    $data['pintu'] = 0;
    file_put_contents($file, json_encode($data));
}

// 6. Mode Control (normal, night, away)
if (isset($_GET['mode'])) {
    $new_mode = $_GET['mode'];
    $valid_modes = ['normal', 'night', 'away'];
    
    if (in_array($new_mode, $valid_modes)) {
        $data['mode'] = $new_mode;
        
        // Apply mode-specific automations
        if ($new_mode === 'away') {
            // Away mode: Lock door + turn off all lights except L6 (porch)
            $data['pintu'] = 1; // Lock door
            $data['L1'] = 0;
            $data['L2'] = 0;
            $data['L3'] = 0;
            $data['L4'] = 0;
            $data['L5'] = 0;
            // L6 (porch light) tetap nyala untuk keamanan
        }
        
        if ($new_mode === 'night') {
            // Night mode: Dim atau turn off certain lights
            $data['L4'] = 0; // Turn off bedroom lights automatically
        }
        
        file_put_contents($file, json_encode($data));
    }
}

echo json_encode($data);
?>