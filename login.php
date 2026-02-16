<?php
session_start();
$username_benar = "admin";
$password_benar = "admin123"; 
$error = "";

if (isset($_POST['login'])) {
    if ($_POST['username'] == $username_benar && $_POST['password'] == $password_benar) {
        $_SESSION['sudah_login'] = true;
        header("Location: index.php");
        exit();
    } else {
        $error = "Oops! Password salah 🥺";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login 🌸</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Quicksand', sans-serif; background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .login-box { background: white; padding: 40px; border-radius: 30px; box-shadow: 0 10px 30px rgba(255, 64, 129, 0.2); text-align: center; width: 90%; max-width: 350px; border: 1px solid rgba(255, 255, 255, 0.8); }
        .login-input { width: 100%; padding: 15px; margin: 10px 0; border: 2px solid #ffebee; border-radius: 15px; background: #fffafa; font-family: 'Quicksand', sans-serif; font-size: 1rem; outline: none; transition: 0.3s; }
        .login-input:focus { border-color: #ff80ab; background: white; box-shadow: 0 0 8px rgba(255, 128, 171, 0.3); }
        .btn { width: 100%; padding: 15px; margin-top: 20px; border: none; border-radius: 50px; background: linear-gradient(45deg, #ff4081, #f50057); color: white; font-weight: bold; font-size: 1rem; cursor: pointer; box-shadow: 0 5px 15px rgba(245, 0, 87, 0.3); transition: transform 0.2s; }
        .btn:active { transform: scale(0.95); }
        h2 { color: #ff4081; margin-bottom: 5px; font-size: 1.8rem; }
        p { color: #aaa; margin-bottom: 25px; }
        .avatar { font-size: 3.5rem; margin-bottom: 10px; display: inline-block; animation: bounce 2s infinite; }
        @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="avatar">🎀</div>
        <h2>Welcome Back!</h2>
        <p>Silakan masuk dulu ya</p>
        <?php if($error) { echo "<p style='color:#ff4081; font-weight:bold; font-size:0.9rem;'>$error</p>"; } ?>
        <form method="POST">
            <input type="text" name="username" class="login-input" placeholder="Username" required autocomplete="off">
            <input type="password" name="password" class="login-input" placeholder="Password" required>
            <button type="submit" name="login" class="btn">MASUK 🚀</button>
        </form>
    </div>
</body>
</html>