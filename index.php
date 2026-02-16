<?php 
session_start();
require_once 'db_init.php';

// Support both old dan new login system
if (!isset($_SESSION['sudah_login']) && !isset($_SESSION['username'])) { 
    header("Location: login_new.php"); 
    exit(); 
}

$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>My Sweet Home 🌸</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff80ab; --bg-gradient: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%); --glass: rgba(255, 255, 255, 0.9); --shadow: 0 8px 32px 0 rgba(255, 105, 180, 0.15); }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Quicksand', sans-serif; background: var(--bg-gradient); min-height: 100vh; padding: 20px; display: flex; justify-content: center; transition: background 0.3s; }
        body.night-mode { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); }
        .container { width: 100%; max-width: 480px; margin: 0 auto; }
        .header h2 { text-align: center; font-size: 2rem; background: linear-gradient(45deg, #ff4081, #ff80ab); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: #ff4081; margin-bottom: 20px; }
        body.night-mode .header h2 { -webkit-text-fill-color: #ff80ab; }
        
        .time-card { background: white; border-radius: 20px; padding: 15px 20px; margin-bottom: 20px; box-shadow: var(--shadow); display: flex; align-items: center; justify-content: space-between; border: 2px solid #fff0f5; }
        body.night-mode .time-card { background: #2a2a3e; border-color: #3a3a5e; color: #fff; }
        .time-info h3 { color: #ff4081; margin: 0; font-size: 1.2rem; }
        .time-info p { color: #888; font-size: 0.9rem; margin: 0; }
        body.night-mode .time-info p { color: #aaa; }
        .time-icon { font-size: 2.5rem; }

        .temp-card { background: linear-gradient(45deg, #ff80ab, #ff4081); border-radius: 25px; padding: 25px; color: white; text-align: center; margin-bottom: 25px; box-shadow: 0 10px 20px rgba(245, 0, 87, 0.3); }
        
        /* MODE SELECTOR CARD */
        .mode-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            border: 2px solid #ff4081;
        }
        body.night-mode .mode-card { background: #2a2a3e; border-color: #ff80ab; }
        .mode-card h3 { color: #ff4081; margin-bottom: 15px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px; }
        body.night-mode .mode-card h3 { color: #ff80ab; }
        
        .mode-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
        }
        .mode-btn {
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 15px;
            background: #f9f9f9;
            color: #555;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Quicksand';
            font-size: 0.9rem;
        }
        body.night-mode .mode-btn { background: #1a1a2e; border-color: #3a3a5e; color: #aaa; }
        .mode-btn.active {
            background: linear-gradient(45deg, #ff4081, #f50057);
            color: white;
            border-color: #ff4081;
        }
        .mode-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(255, 64, 129, 0.3); }
        
        .grid-container { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
        .card { background: var(--glass); border-radius: 20px; padding: 15px; text-align: center; box-shadow: var(--shadow); display: flex; flex-direction: column; align-items: center; }
        body.night-mode .card { background: #2a2a3e; color: #fff; border: 1px solid #3a3a5e; }
        .icon-box { font-size: 2.2rem; margin-bottom: 8px; }
        .label { font-weight: 700; font-size: 0.9rem; margin-bottom: 12px; color: #555; }
        body.night-mode .label { color: #aaa; }
        
        .btn { width: 100%; padding: 10px; border-radius: 50px; border: none; cursor: pointer; font-weight: 700; transition: 0.3s; font-family: 'Quicksand'; }
        .btn-on { background: #f0f0f0; color: #888; }
        body.night-mode .btn-on { background: #1a1a2e; color: #666; border: 1px solid #3a3a5e; }
        .btn-off { background: linear-gradient(45deg, #ff4081, #f50057); color: white; box-shadow: 0 4px 10px rgba(245, 0, 87, 0.4); }
        .glow-on { filter: drop-shadow(0 0 5px #ff4081); transform: scale(1.1); }
        .dim-off { filter: grayscale(100%); opacity: 0.4; }
        
        .door-card { background: white; border-radius: 20px; padding: 20px; text-align: center; margin-bottom: 30px; box-shadow: var(--shadow); }
        body.night-mode .door-card { background: #2a2a3e; border: 1px solid #3a3a5e; }
        .btn-door { background: linear-gradient(45deg, #b388ff, #7c4dff); color: white; padding: 12px; box-shadow: 0 5px 15px rgba(124, 77, 255, 0.3); }
        .btn-door:active { transform: scale(0.95); }
        #door-status-text { font-weight: bold; font-size: 1.1rem; margin-bottom: 15px; display: block; color: #555; }
        body.night-mode #door-status-text { color: #aaa; }
        .status-open { color: #e040fb !important; animation: pulse 1s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        
        .logout { text-align: center; margin-bottom: 20px; }
        .logout a { color: #ff4081; text-decoration: none; font-weight: bold; border: 2px solid #ff4081; padding: 8px 20px; border-radius: 20px; }
        h4 { color: #ff80ab; margin-bottom: 15px; margin-left: 5px; font-weight: 700; }
        body.night-mode h4 { color: #ffb3d9; }
        
        /* PIR CARD STYLING */
        .pir-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            text-align: left;
            border: 2px solid #9c27b0;
        }
        body.night-mode .pir-card { background: #2a2a3e; border-color: #b366ff; }
        .pir-card input[type="checkbox"], 
        .pir-card input[type="number"] {
            background: white;
            color: #333;
        }
        body.night-mode .pir-card input[type="checkbox"],
        body.night-mode .pir-card input[type="number"] {
            background: #1a1a2e;
            color: #fff;
            border-color: #3a3a5e;
        }
        .pir-card label { font-size: 0.95rem; }
        
        #motion-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            border: 2px solid #ff4081;
            animation: pulse 1s infinite;
            text-align: center;
            display: none;
        }
        body.night-mode #motion-card { background: #2a2a3e; }
        #motion-card div:first-child { font-size: 2.5rem; margin-bottom: 10px; }
        #motion-card .label { color: #ff4081; font-size: 1.2rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="header"><h2>✨ Sweet Home ✨</h2></div>
    
    <div class="time-card">
        <div class="time-info"><h3 id="greeting">Halo! 👋</h3><p id="clock">Memuat...</p></div>
        <div class="time-icon" id="time-icon">☀️</div>
    </div>

    <div class="temp-card">
        <div class="temp-val"><span id="suhu">--</span>°C</div>
        <div>💧 Kelembaban: <span id="hum">--</span>%</div>
        <div style="font-size: 0.7em; margin-top: 10px; opacity: 0.8;">Updated: <span id="last">--</span></div>
    </div>

    <!-- MODE SELECTOR CARD -->
    <div class="mode-card">
        <h3>🎛️ Mode Rumah</h3>
        <div class="mode-buttons">
            <button class="mode-btn active" data-mode="normal" onclick="changeMode('normal')">
                ☀️ Normal
            </button>
            <button class="mode-btn" data-mode="night" onclick="changeMode('night')">
                🌙 Tidur
            </button>
            <button class="mode-btn" data-mode="away" onclick="changeMode('away')">
                🚪 Keluar
            </button>
        </div>
    </div>
    <div style="background: linear-gradient(45deg, #ffd54f, #ffb74d); border-radius: 20px; padding: 20px; color: white; margin-bottom: 25px; box-shadow: 0 10px 20px rgba(255, 180, 0, 0.3); text-align: center;">
        <div style="font-size: 2.5rem; margin-bottom: 10px;">☀️</div>
        <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 10px;">Sensor Cahaya (LDR)</div>
        <div style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;"><span id="ldr-value">--</span></div>
        <div style="font-size: 0.9rem; opacity: 0.9;">
            <span id="ldr-status">Status: Terang</span>
            <span id="ldr-indicator" style="margin-left: 10px;">☀️</span>
        </div>
    </div>

    <h4 style="margin-left: 5px;">💡 Daftar Lampu</h4>
    <div id="lamp-list" class="grid-container"></div>

    <!-- MOTION DETECTION CARD -->
    <div id="motion-card">
        <div>🏃💨</div>
        <div class="label">GERAKAN TERDETEKSI!</div>
        <span style="font-size: 0.9rem;">Ada aktivitas di dalam rumah</span>
    </div>

    <!-- PIR SETTINGS CARD -->
    <div class="pir-card">
        <div style="font-size: 2rem; margin-bottom: 10px;">🏃</div>
        <div style="color: #9c27b0; font-weight: 700; margin-bottom: 15px;">Pengaturan Sensor Gerakan (PIR)</div>
        <div style="margin-bottom: 15px;">
            <label style="color: #555; font-weight: 600; display: block; cursor: pointer;">
                <input type="checkbox" id="pir-auto-off" checked onchange="togglePirAutoOff()" style="margin-right: 8px; cursor: pointer;">
                Matikan Lampu Otomatis saat Tidak Ada Gerakan
            </label>
        </div>
        <div style="margin-bottom: 15px;">
            <label for="pir-timeout" style="color: #555; font-weight: 600; display: block; margin-bottom: 8px;">
                Timeout Deteksi (detik):
            </label>
            <input type="number" id="pir-timeout" min="10" max="600" step="10" value="90" 
                   style="width: 100%; padding: 10px; border: 2px solid #9c27b0; border-radius: 10px; font-size: 1rem;" />
            <button onclick="setPirTimeout()" class="btn btn-off" style="margin-top: 10px; background: #9c27b0;">
                💾 Simpan Timeout
            </button>
        </div>
        <div style="font-size: 0.85rem; color: #888; margin-top: 10px;">
            Status PIR: <span id="pir-status-text" style="color: #9c27b0; font-weight: 600;">--</span>
        </div>
    </div>

    <!-- LDR SETTINGS CARD -->
    <div class="pir-card" style="border-color: #ffb74d;">
        <div style="font-size: 2rem; margin-bottom: 10px;">☀️</div>
        <div style="color: #ff9800; font-weight: 700; margin-bottom: 15px;">Pengaturan Sensor Cahaya (LDR)</div>
        <div style="margin-bottom: 15px;">
            <label style="color: #555; font-weight: 600; display: block; cursor: pointer;">
                <input type="checkbox" id="ldr-auto-on" checked onchange="toggleLdrAutoOn()" style="margin-right: 8px; cursor: pointer;">
                Nyalakan Lampu Teras Otomatis saat Gelap
            </label>
        </div>
        <div style="margin-bottom: 15px;">
            <label for="ldr-threshold" style="color: #555; font-weight: 600; display: block; margin-bottom: 8px;">
                Tingkat Kecerahan Ambang (0-1023):
            </label>
            <input type="number" id="ldr-threshold" min="0" max="1023" step="10" value="400" 
                   style="width: 100%; padding: 10px; border: 2px solid #ffb74d; border-radius: 10px; font-size: 1rem;" />
            <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                ⚠️ Nilai lebih rendah = lebih sensitif (lebih mudah gelap)
            </div>
            <button onclick="setLdrThreshold()" class="btn btn-off" style="margin-top: 10px; background: #ff9800;">
                💾 Simpan Ambang
            </button>
        </div>
        <div style="font-size: 0.85rem; color: #888; margin-top: 10px;">
            Nilai LDR: <span id="ldr-value-text" style="color: #ff9800; font-weight: 600;">--</span>
        </div>
    </div>

    <div class="door-card">
        <div style="font-size: 2.5rem; margin-bottom: 5px;">🚪</div>
        <div class="label">Akses Pintu Utama</div>
        <span id="door-status-text">Status: Terkunci 🔒</span>
        <button onclick="toggle('pintu', 1)" class="btn btn-door">✨ BUKA PINTU ✨</button>
    </div>

    <div class="logout"><a href="logout_new.php">Keluar 🎀</a>
    <?php if($is_admin): ?><a href="admin_panel.php" style="margin-left: 10px;">👨‍💼 Admin Panel</a><?php endif; ?>
    </div>
</div>

<script>
    const rooms = {
        'L1': { name: 'R. Tamu', icon: '🛋️' }, 'L2': { name: 'Kamar Tidur', icon: '🛏️' },
        'L3': { name: 'Kamar Tidur', icon: '🛏️' }, 'L4': { name: 'K. Mandi', icon: '🛁' },
        'L5': { name: 'Wardrobe', icon: '👗' }, 'L6': { name: 'Teras Luar', icon: '🌃' }
    };

    function updateTime() {
        const now = new Date();
        const hour = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        document.getElementById('clock').innerText = `Jam: ${hour}:${minutes}`;
        let greeting = "", icon = "";
        
        if (hour >= 4 && hour < 10) { greeting = "Selamat Pagi 🌼"; icon = "🌅"; }
        else if (hour >= 10 && hour < 15) { greeting = "Selamat Siang ☀️"; icon = "🏖️"; }
        else if (hour >= 15 && hour < 18) { greeting = "Selamat Sore 🌇"; icon = "☕"; }
        else { greeting = "Selamat Malam 🌙"; icon = "✨"; }
        
        document.getElementById('greeting').innerText = greeting;
        document.getElementById('time-icon').innerText = icon;
    }
    setInterval(updateTime, 1000); updateTime();

    function render(data) {
        if(!data) return;
        
        // Update mode UI and theme
        if (data.mode) {
            updateModeUI(data.mode);
            applyModeTheme(data.mode);
        }
        
        let html = '';
        for(let i=1; i<=6; i++) {
            let id = 'L'+i;
            let info = rooms[id] || {name: 'Lampu '+i, icon: '💡'};
            let isOn = parseInt(data[id]) === 1;
            let btnClass = isOn ? 'btn-off' : 'btn-on'; 
            let btnText = isOn ? 'ON ✨' : 'OFF';
            let iconClass = isOn ? 'glow-on' : 'dim-off';
            html += `<div class="card"><div class="icon-box ${iconClass}">${info.icon}</div><div class="label">${info.name}</div><button onclick="toggle('${id}', ${isOn ? 0 : 1})" class="btn ${btnClass}">${btnText}</button></div>`;
        }
        document.getElementById('lamp-list').innerHTML = html;
        if(data.suhu) document.getElementById('suhu').innerText = data.suhu;
        if(data.kelembaban) document.getElementById('hum').innerText = data.kelembaban;
        if(data.last_update) document.getElementById('last').innerText = data.last_update;

        let statusText = document.getElementById('door-status-text');
        if(parseInt(data.pintu) === 1) {
            statusText.innerHTML = "Status: Membuka... 🔓"; statusText.classList.add('status-open');
        } else {
            statusText.innerHTML = "Status: Terkunci 🔒"; statusText.classList.remove('status-open');
        }

        // Update PIR Status & Motion Card
        updatePirStatus(data);
        
        // Update LDR Status
        updateLdrStatus(data);
    }

    function toggle(dev, act) {
        fetch('control.php', { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: `device=${dev}&action=${act}` }).then(() => loadData());
    }
    function loadData() { fetch('get_status.php').then(r => r.json()).then(render); }
    setInterval(loadData, 2000); loadData();

    // --- PIR AUTO-OFF SETTINGS ---
    function togglePirAutoOff() {
        let checkbox = document.getElementById('pir-auto-off');
        let enabled = checkbox.checked ? 1 : 0;
        fetch('get_status.php')
            .then(r => r.json())
            .then(() => {
                fetch('api.php?pir_auto_off_enabled=' + enabled)
                    .then(() => loadData())
                    .catch(e => console.error('PIR toggle error:', e));
            });
    }

    function setPirTimeout() {
        let timeout = parseInt(document.getElementById('pir-timeout').value);
        if (timeout < 10 || timeout > 600) {
            alert('Timeout harus antara 10-600 detik');
            return;
        }
        fetch('api.php?pir_timeout=' + timeout)
            .then(() => {
                alert('Timeout PIR diatur ke ' + timeout + ' detik');
                loadData();
            })
            .catch(e => console.error('PIR timeout error:', e));
    }

    function updatePirStatus(data) {
        let statusText = document.getElementById('pir-status-text');
        let checkbox = document.getElementById('pir-auto-off');
        let timeoutInput = document.getElementById('pir-timeout');
        let motionCard = document.getElementById('motion-card');
        
        // Update motion card display
        if (data.pir === 1) {
            motionCard.style.display = 'block';
            statusText.innerText = '🏃 Gerakan Terdeteksi!';
        } else {
            motionCard.style.display = 'none';
            statusText.innerText = '😴 Tidak Ada Gerakan';
        }
        
        // Update checkbox
        if (data.pir_auto_off_enabled !== undefined) {
            checkbox.checked = (parseInt(data.pir_auto_off_enabled) === 1);
        }
        
        // Update timeout input
        if (data.pir_timeout !== undefined) {
            timeoutInput.value = data.pir_timeout;
        }
    }

    // --- LDR SENSOR FUNCTIONS ---
    function updateLdrStatus(data) {
        if (!data.ldr) return;
        
        let ldrValue = parseInt(data.ldr);
        let ldrThreshold = parseInt(data.ldr_threshold) || 400;
        let ldrAutoOn = parseInt(data.ldr_auto_on) || 1;
        
        // Update LDR display card
        document.getElementById('ldr-value').innerText = ldrValue;
        document.getElementById('ldr-value-text').innerText = ldrValue;
        
        // Update LDR status indicator
        let statusText = document.getElementById('ldr-status');
        let indicator = document.getElementById('ldr-indicator');
        
        if (ldrValue < ldrThreshold) {
            statusText.innerText = '🌙 Gelap - Lampu Teras Menyala';
            indicator.innerText = '🌙';
        } else {
            statusText.innerText = '☀️ Terang - Lampu Teras Mati';
            indicator.innerText = '☀️';
        }
        
        // Update LDR settings
        let ldrCheckbox = document.getElementById('ldr-auto-on');
        let ldrThresholdInput = document.getElementById('ldr-threshold');
        
        if (ldrCheckbox) {
            ldrCheckbox.checked = (ldrAutoOn === 1);
        }
        if (ldrThresholdInput) {
            ldrThresholdInput.value = ldrThreshold;
        }
    }

    function toggleLdrAutoOn() {
        let checkbox = document.getElementById('ldr-auto-on');
        let enabled = checkbox.checked ? 1 : 0;
        fetch('api.php?ldr_auto_on=' + enabled)
            .then(r => r.json())
            .then(() => loadData())
            .catch(e => console.error('LDR toggle error:', e));
    }

    function setLdrThreshold() {
        let threshold = parseInt(document.getElementById('ldr-threshold').value);
        if (threshold < 0 || threshold > 1023) {
            alert('Ambang harus antara 0-1023');
            return;
        }
        fetch('api.php?ldr_threshold=' + threshold)
            .then(() => {
                alert('Ambang LDR diatur ke ' + threshold);
                loadData();
            })
            .catch(e => console.error('LDR threshold error:', e));
    }

    // --- MODE CONTROL ---
    function changeMode(mode) {
        fetch('api.php?mode=' + mode)
            .then(r => r.json())
            .then(data => {
                updateModeUI(data.mode || 'normal');
                applyModeTheme(data.mode || 'normal');
                loadData();
            })
            .catch(e => console.error('Mode change error:', e));
    }

    function updateModeUI(currentMode) {
        document.querySelectorAll('.mode-btn').forEach(btn => {
            if (btn.dataset.mode === currentMode) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    function applyModeTheme(mode) {
        const body = document.body;
        if (mode === 'night') {
            body.classList.add('night-mode');
        } else {
            body.classList.remove('night-mode');
        }
        
        // Save mode preference to localStorage
        localStorage.setItem('preferredMode', mode);
    }

    // Initialize theme on page load
    document.addEventListener('DOMContentLoaded', () => {
        let savedMode = localStorage.getItem('preferredMode') || 'normal';
        applyModeTheme(savedMode);
    });

</script>
</body>
</html>