<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'casino_access_system');

// Configuration du site
define('SITE_NAME', 'Casino Access System');
define('SITE_URL', 'http://localhost/casino-access-system/');
define('ADMIN_URL', 'http://localhost/casino-access-system/admin/');

// Configuration des jeux
define('KENO_GAME_URL', 'games/keno/index.html');
define('SLOTS_GAME_URL', 'games/slots/index.html');

// Configuration sécurité
define('CODE_LENGTH', 12);
define('CODE_PREFIX', 'CAS');
define('SESSION_TIMEOUT', 3600); // 1 heure

// Démarrer la session
session_start();

// Connexion à la base de données
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    return $db;
}

// Fonction de redirection
function redirect($url) {
    header("Location: $url");
    exit();
}

// Vérifier si admin est connecté
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Vérifier si joueur est connecté
function isPlayerLoggedIn() {
    return isset($_SESSION['player_code']) && !empty($_SESSION['player_code']);
}

// Générer un code aléatoire
function generateAccessCode() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = CODE_PREFIX . '-';
    
    for ($i = 0; $i < CODE_LENGTH; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $code;
}

// Formater l'argent
function formatMoney($amount) {
    return number_format($amount, 2, ',', ' ') . ' €';
}
?>
