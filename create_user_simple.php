<?php

// Simple direct database insertion for admin user
$databasePath = __DIR__ . '/var/eventspot.db';

try {
    // Create PDO connection to SQLite
    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
    $stmt->execute(['admin@eventspot.fr']);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "Admin user already exists!\n";
    } else {
        // Insert admin user with properly hashed password
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO user (email, pseudo, roles, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            'admin@eventspot.fr',
            'Admin', 
            '["ROLE_ADMIN"]',
            $password
        ]);
        
        echo "Admin user created successfully!\n";
        echo "Email: admin@eventspot.fr\n";
        echo "Password: admin123\n";
    }

    // Verify user was created
    $stmt = $pdo->prepare("SELECT email, pseudo, roles FROM user WHERE email = ?");
    $stmt->execute(['admin@eventspot.fr']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "\nUser verification:\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Pseudo: " . $user['pseudo'] . "\n";
        echo "Roles: " . $user['roles'] . "\n";
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
