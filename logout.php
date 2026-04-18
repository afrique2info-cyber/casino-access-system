<?php
require_once 'config.php';

// Déconnexion joueur
if (isset($_SESSION['player_code'])) {
    unset($_SESSION['player_code']);
    unset($_SESSION['player_amount']);
    unset($_SESSION['code_id']);
}

// Déconnexion admin
if (isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_username']);
}

session_destroy();

// Redirection vers l'accueil
redirect('index.php');
