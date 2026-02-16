<?php
session_start();
require_once 'db_init.php';

// Check authentication dan admin role
if (!isset($_SESSION['username'])) {
    header("Location: login_new.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    die("❌ Akses ditolak - Hanya admin yang dapat mengakses halaman ini");
}

// Ambil daftar users
$stmt = $db->query("SELECT id, username, role, email, created_at, status FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Panel - Smart Home 🏠</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: linear-gradient(135deg, #fce4ec 0%, #f3e5f5 100%);
            padding: 20px; 
            min-height: 100vh;
        }
        
        .container { max-width: 1200px; margin: 0 auto; }
        
        .header {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
            padding: 30px;
            border-radius: 25px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 32px rgba(255, 64, 129, 0.25);
        }
        
        .header h1 { 
            font-size: 2rem; 
            font-weight: 700;
        }
        
        .header p { 
            opacity: 0.95; 
            margin-top: 5px; 
            font-size: 0.95rem;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.25);
            border: 2px solid white;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            font-family: 'Quicksand', sans-serif;
        }
        
        .logout-btn:hover {
            background: white;
            color: #ff4081;
            transform: translateY(-2px);
        }
        
        .section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(255, 105, 180, 0.15);
            border: 1px solid rgba(255, 192, 203, 0.3);
        }
        
        .section h2 {
            color: #ff4081;
            margin-bottom: 25px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
        }
        
        /* FORM SECTION */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-weight: 600;
            color: #ff4081;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        input, select {
            padding: 12px 15px;
            border: 2px solid #ffcdd2;
            border-radius: 12px;
            font-family: 'Quicksand', sans-serif;
            font-size: 1rem;
            transition: 0.3s;
            background: #fffafa;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #ff80ab;
            background: white;
            box-shadow: 0 0 12px rgba(255, 128, 171, 0.2);
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            font-family: 'Quicksand', sans-serif;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #ff4081, #f50057);
            color: white;
            width: 100%;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(245, 0, 87, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 0, 87, 0.4);
        }
        
        .btn-danger {
            background: #ff6b9d;
            color: white;
        }
        
        .btn-danger:hover {
            background: #ff5a8a;
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: #ffb74d;
            color: white;
        }
        
        .btn-warning:hover {
            background: #ff9800;
            transform: translateY(-2px);
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
        
        /* TABLE SECTION */
        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            border: 2px solid #ffcdd2;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: linear-gradient(45deg, #fff0f5, #fce4ec);
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #ff4081;
            border-bottom: 2px solid #ffcdd2;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #ffe0e6;
            color: #333;
        }
        
        tr:hover {
            background: #fff5f8;
        }
        
        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-admin {
            background: linear-gradient(45deg, #ff4081, #ff80ab);
            color: white;
        }
        
        .badge-user {
            background: #f8bbd0;
            color: #c2185b;
        }
        
        .badge-active {
            background: #c8e6c9;
            color: #2e7d32;
        }
        
        .badge-inactive {
            background: #e0e0e0;
            color: #666;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .action-buttons .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        /* ALERT MESSAGES */
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #c8e6c9;
            color: #2e7d32;
            border-color: #4caf50;
        }
        
        .alert-error {
            background: #ffcccc;
            color: #c2185b;
            border-color: #ff4081;
        }
        
        .alert.show {
            display: block;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }
        
        .modal.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .modal-content {
            background: white;
            padding: 35px;
            border-radius: 25px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(255, 64, 129, 0.25);
            border: 1px solid rgba(255, 192, 203, 0.3);
        }
        
        .modal-header {
            margin-bottom: 25px;
        }
        
        .modal-header h3 {
            color: #ff4081;
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .close-btn {
            float: right;
            font-size: 2rem;
            cursor: pointer;
            color: #ff80ab;
            background: none;
            border: none;
            transition: 0.2s;
        }
        
        .close-btn:hover {
            color: #ff4081;
            transform: scale(1.1);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 40px;
            color: #ff80ab;
        }
        
        .empty-state-icon {
            font-size: 3.5rem;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            color: #ff4081;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            td, th {
                padding: 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- HEADER -->
    <div class="header">
        <div>
            <h1>👨‍💼 Admin Panel</h1>
            <p>Kelola pengguna sistem Smart Home</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <a href="index.php" class="logout-btn">← Dashboard</a>
            <a href="logout_new.php" class="logout-btn">Keluar 🚪</a>
        </div>
    </div>
    
    <!-- ALERT MESSAGES -->
    <div id="alert" class="alert"></div>
    
    <!-- ADD USER SECTION -->
    <div class="section">
        <h2>➕ Tambah User Baru</h2>
        
        <form id="addUserForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        placeholder="contoh: john_doe" 
                        required
                        minlength="3">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="minimal 6 karakter" 
                        required
                        minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="user@example.com">
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role">
                        <option value="user">User Biasa</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">✨ Tambah User</button>
        </form>
    </div>
    
    <!-- USERS LIST SECTION -->
    <div class="section">
        <h2>👥 Daftar Pengguna</h2>
        
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">👤</div>
                <p>Belum ada pengguna</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="usersList">
                        <?php foreach($users as $user): ?>
                            <tr id="user-<?php echo $user['id']; ?>">
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php echo $user['role'] === 'admin' ? '👨‍💼 Admin' : '👤 User'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo ($user['status'] ? 'active' : 'inactive'); ?>">
                                        <?php echo $user['status'] ? '✅ Aktif' : '🔒 Nonaktif'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="editUser(<?php echo $user['id']; ?>)">✏️ Edit</button>
                                        <button class="btn btn-sm btn-warning" onclick="resetPassword(<?php echo $user['id']; ?>)">🔑 Reset Pass</button>
                                        <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                            <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">🗑️ Hapus</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <button class="close-btn" onclick="closeModal('editModal')">&times;</button>
            <h3>Edit User</h3>
        </div>
        
        <form id="editUserForm">
            <input type="hidden" id="editUserId">
            
            <div class="form-group">
                <label for="editEmail">Email</label>
                <input type="email" id="editEmail" name="email">
            </div>
            
            <div class="form-group">
                <label for="editRole">Role</label>
                <select id="editRole" name="role">
                    <option value="user">User Biasa</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="editStatus">Status</label>
                <select id="editStatus" name="status">
                    <option value="1">✅ Aktif</option>
                    <option value="0">🔒 Nonaktif</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        </form>
    </div>
</div>

<!-- RESET PASSWORD MODAL -->
<div id="resetModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <button class="close-btn" onclick="closeModal('resetModal')">&times;</button>
            <h3>Reset Password</h3>
        </div>
        
        <form id="resetPasswordForm">
            <input type="hidden" id="resetUserId">
            
            <div class="form-group">
                <label for="newPassword">Password Baru</label>
                <input 
                    type="password" 
                    id="newPassword" 
                    name="new_password" 
                    placeholder="minimal 6 karakter"
                    required
                    minlength="6">
            </div>
            
            <button type="submit" class="btn btn-primary">🔑 Reset Password</button>
        </form>
    </div>
</div>

<script>
// Show Alert
function showAlert(message, type = 'success') {
    const alert = document.getElementById('alert');
    alert.textContent = message;
    alert.className = `alert alert-${type} show`;
    
    setTimeout(() => {
        alert.classList.remove('show');
    }, 5000);
}

// Modal Functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

// Click outside modal to close
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const resetModal = document.getElementById('resetModal');
    
    if (event.target === editModal) {
        closeModal('editModal');
    }
    if (event.target === resetModal) {
        closeModal('resetModal');
    }
}

// ADD USER
document.getElementById('addUserForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('username', document.getElementById('username').value);
    formData.append('password', document.getElementById('password').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('role', document.getElementById('role').value);
    
    try {
        const response = await fetch('admin_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('✅ ' + result.message, 'success');
            document.getElementById('addUserForm').reset();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('❌ ' + result.message, 'error');
        }
    } catch (error) {
        showAlert('❌ Error: ' + error.message, 'error');
    }
});

// EDIT USER
function editUser(userId) {
    const row = document.getElementById('user-' + userId);
    const cells = row.querySelectorAll('td');
    
    document.getElementById('editUserId').value = userId;
    document.getElementById('editEmail').value = cells[1].textContent;
    document.getElementById('editRole').value = cells[2].textContent.includes('Admin') ? 'admin' : 'user';
    document.getElementById('editStatus').value = cells[3].textContent.includes('Aktif') ? '1' : '0';
    
    openModal('editModal');
}

document.getElementById('editUserForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'edit');
    formData.append('user_id', document.getElementById('editUserId').value);
    formData.append('email', document.getElementById('editEmail').value);
    formData.append('role', document.getElementById('editRole').value);
    formData.append('status', document.getElementById('editStatus').value);
    
    try {
        const response = await fetch('admin_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('✅ ' + result.message, 'success');
            closeModal('editModal');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('❌ ' + result.message, 'error');
        }
    } catch (error) {
        showAlert('❌ Error: ' + error.message, 'error');
    }
});

// RESET PASSWORD
function resetPassword(userId) {
    document.getElementById('resetUserId').value = userId;
    openModal('resetModal');
}

document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'reset_password');
    formData.append('user_id', document.getElementById('resetUserId').value);
    formData.append('new_password', document.getElementById('newPassword').value);
    
    try {
        const response = await fetch('admin_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('✅ ' + result.message, 'success');
            closeModal('resetModal');
            document.getElementById('resetPasswordForm').reset();
        } else {
            showAlert('❌ ' + result.message, 'error');
        }
    } catch (error) {
        showAlert('❌ Error: ' + error.message, 'error');
    }
});

// DELETE USER
function deleteUser(userId) {
    if (confirm('Yakin ingin menghapus user ini? Aksi ini tidak bisa dibatalkan!')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('user_id', userId);
        
        fetch('admin_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showAlert('✅ ' + result.message, 'success');
                document.getElementById('user-' + userId).remove();
            } else {
                showAlert('❌ ' + result.message, 'error');
            }
        })
        .catch(error => showAlert('❌ Error: ' + error.message, 'error'));
    }
}
</script>

</body>
</html>
