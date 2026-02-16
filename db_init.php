<?php
/**
 * Database initialization & connection
 * Untuk Smart Home User Management
 */

$db_file = 'users.db';

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table jika belum ada
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        email TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status INTEGER DEFAULT 1
    )");
    
    // Insert default admin jika belum ada
    $check = $db->query("SELECT * FROM users WHERE username='admin'")->fetch();
    if (!$check) {
        $admin_pass = password_hash('admin123', PASSWORD_DEFAULT);
        $db->exec("INSERT INTO users (username, password, role, email) 
                   VALUES ('admin', '$admin_pass', 'admin', 'admin@smarthome.local')");
    }
    
} catch (Exception $e) {
    die("Database Error: " . $e->getMessage());
}
?>
