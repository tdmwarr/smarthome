<?php
session_start();
require_once 'db_init.php';
header('Content-Type: application/json');

// Check admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Akses ditolak - Admin only']));
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    // GET: Daftar semua users
    if ($action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $db->query("SELECT id, username, role, email, created_at, status FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $users]);
    }
    
    // POST: Tambah user baru
    else if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        
        // Validasi
        if (strlen($username) < 3) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username minimal 3 karakter']);
            exit;
        }
        
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
            exit;
        }
        
        if (!in_array($role, ['admin', 'user'])) {
            $role = 'user';
        }
        
        // Cek username sudah ada
        $check = $db->prepare("SELECT id FROM users WHERE username=?");
        $check->execute([$username]);
        if ($check->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
            exit;
        }
        
        // Tambah user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password, role, email, status) VALUES (?, ?, ?, ?, 1)");
        
        if ($stmt->execute([$username, $hashed_password, $role, $email])) {
            echo json_encode([
                'success' => true, 
                'message' => 'User berhasil ditambahkan',
                'user_id' => $db->lastInsertId()
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menambah user']);
        }
    }
    
    // POST: Edit user
    else if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = intval($_POST['user_id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $status = intval($_POST['status'] ?? 1);
        
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID tidak valid']);
            exit;
        }
        
        if (!in_array($role, ['admin', 'user'])) {
            $role = 'user';
        }
        
        $stmt = $db->prepare("UPDATE users SET email=?, role=?, status=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
        
        if ($stmt->execute([$email, $role, $status, $user_id])) {
            echo json_encode(['success' => true, 'message' => 'User berhasil diupdate']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal update user']);
        }
    }
    
    // POST: Hapus user
    else if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = intval($_POST['user_id'] ?? 0);
        
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID tidak valid']);
            exit;
        }
        
        // Jangan bisa delete akun sendiri
        if ($user_id === $_SESSION['user_id']) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri']);
            exit;
        }
        
        $stmt = $db->prepare("DELETE FROM users WHERE id=?");
        
        if ($stmt->execute([$user_id])) {
            echo json_encode(['success' => true, 'message' => 'User berhasil dihapus']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal hapus user']);
        }
    }
    
    // POST: Reset password
    else if ($action === 'reset_password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = intval($_POST['user_id'] ?? 0);
        $new_password = $_POST['new_password'] ?? '';
        
        if ($user_id <= 0 || strlen($new_password) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            exit;
        }
        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
        
        if ($stmt->execute([$hashed_password, $user_id])) {
            echo json_encode(['success' => true, 'message' => 'Password berhasil direset']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal reset password']);
        }
    }
    
    else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
