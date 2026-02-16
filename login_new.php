<?php
session_start();
require_once 'db_init.php';

// Redirect jika sudah login
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

// Handle login
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        $sql = "SELECT * FROM users WHERE username=? AND status=1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Username atau password salah! 🥺";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Smart Home 🌸</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%); 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 20px;
        }
        .login-box { 
            background: white; 
            padding: 40px; 
            border-radius: 30px; 
            box-shadow: 0 10px 30px rgba(255, 64, 129, 0.2); 
            text-align: center; 
            width: 100%; 
            max-width: 400px; 
            border: 1px solid rgba(255, 192, 203, 0.3);
        }
        .avatar { font-size: 4rem; margin-bottom: 10px; animation: bounce 2s infinite; }
        h2 { color: #ff4081; margin-bottom: 5px; font-size: 1.8rem; font-weight: 700; }
        p { color: #ff80ab; margin-bottom: 25px; font-size: 0.95rem; }
        
        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; color: #ff4081; font-weight: 600; margin-bottom: 5px; font-size: 0.9rem; }
        .login-input { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #ffcdd2; 
            border-radius: 12px; 
            background: #fffafa; 
            font-family: 'Quicksand', sans-serif; 
            font-size: 1rem; 
            outline: none; 
            transition: 0.3s; 
        }
        .login-input:focus { 
            border-color: #ff80ab; 
            background: white; 
            box-shadow: 0 0 8px rgba(255, 128, 171, 0.3); 
        }
        
        .btn { 
            width: 100%; 
            padding: 12px; 
            margin-top: 20px; 
            border: none; 
            border-radius: 50px; 
            background: linear-gradient(45deg, #ff4081, #f50057); 
            color: white; 
            font-weight: bold; 
            font-size: 1rem; 
            cursor: pointer; 
            box-shadow: 0 5px 15px rgba(245, 0, 87, 0.3); 
            transition: transform 0.2s; 
            font-family: 'Quicksand', sans-serif;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245, 0, 87, 0.4); }
        .btn:active { transform: scale(0.98); }
        
        .error { 
            color: #c2185b; 
            background: #ffcccc; 
            padding: 12px; 
            border-radius: 10px; 
            margin-bottom: 20px; 
            font-size: 0.95rem; 
            border-left: 4px solid #ff4081;
        }
        
        .demo-info { 
            background: #fff3e0; 
            padding: 15px; 
            border-radius: 12px; 
            margin-top: 25px; 
            font-size: 0.85rem; 
            color: #e65100;
            border: 1px solid #ffe0b2;
        }
        .demo-info strong { color: #d84315; }
        
        @keyframes bounce { 
            0%, 100% { transform: translateY(0); } 
            50% { transform: translateY(-10px); } 
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="avatar">🩷</div>
        <h2>Smart Home</h2>
        <p>Silakan masuk untuk melanjutkan</p>
        
        <?php if($error) { 
            echo "<div class='error'>$error</div>"; 
        } ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username"
                    name="username" 
                    class="login-input" 
                    placeholder="Masukkan username" 
                    required 
                    autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password"
                    name="password" 
                    class="login-input" 
                    placeholder="Masukkan password" 
                    required>
            </div>
            
            <button type="submit" name="login" class="btn">MASUK 🚀</button>
        </form>
    </div>
</body>
</html>
