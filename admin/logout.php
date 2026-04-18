<?php
require_once '../config.php';

// Déconnexion admin
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);

session_destroy();

// Redirection vers la page de connexion admin
redirect('login.php');
