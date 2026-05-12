<?php

// Create multiple test users for EventSpot
$databasePath = __DIR__ . '/var/eventspot.db';

try {
    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Clear existing users
    $pdo->exec("DELETE FROM user");
    
    // Create test users
    $users = [
        [
            'email' => 'admin@eventspot.fr',
            'pseudo' => 'Admin',
            'roles' => '["ROLE_ADMIN"]',
            'password' => password_hash('admin123', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'orga1@eventspot.fr', 
            'pseudo' => 'Organisateur1',
            'roles' => '["ROLE_ORGANISATEUR"]',
            'password' => password_hash('orga123', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'orga2@eventspot.fr',
            'pseudo' => 'Organisateur2', 
            'roles' => '["ROLE_ORGANISATEUR"]',
            'password' => password_hash('orga123', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'user1@eventspot.fr',
            'pseudo' => 'Utilisateur1',
            'roles' => '["ROLE_USER"]',
            'password' => password_hash('user123', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'user2@eventspot.fr',
            'pseudo' => 'Utilisateur2',
            'roles' => '["ROLE_USER"]', 
            'password' => password_hash('user123', PASSWORD_DEFAULT)
        ]
    ];

    $stmt = $pdo->prepare("INSERT INTO user (email, pseudo, roles, password) VALUES (?, ?, ?, ?)");
    
    foreach ($users as $user) {
        $stmt->execute([$user['email'], $user['pseudo'], $user['roles'], $user['password']]);
        echo "Created user: {$user['email']} ({$user['pseudo']})\n";
    }

    echo "\n=== EventSpot Test Users Created ===\n\n";
    echo "📋 Available Users:\n\n";
    
    echo "👑 ADMIN:\n";
    echo "   Email: admin@eventspot.fr\n";
    echo "   Password: admin123\n";
    echo "   Role: ROLE_ADMIN\n\n";
    
    echo "🎪 ORGANISATEURS:\n";
    echo "   1. Email: orga1@eventspot.fr\n";
    echo "      Password: orga123\n";
    echo "      Role: ROLE_ORGANISATEUR\n\n";
    echo "   2. Email: orga2@eventspot.fr\n";
    echo "      Password: orga123\n";
    echo "      Role: ROLE_ORGANISATEUR\n\n";
    
    echo "👤 UTILISATEURS:\n";
    echo "   1. Email: user1@eventspot.fr\n";
    echo "      Password: user123\n";
    echo "      Role: ROLE_USER\n\n";
    echo "   2. Email: user2@eventspot.fr\n";
    echo "      Password: user123\n";
    echo "      Role: ROLE_USER\n\n";

    // Verify users were created
    $stmt = $pdo->query("SELECT COUNT(*) FROM user");
    $count = $stmt->fetchColumn();
    echo "✅ Total users created: $count\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
