<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isPlayerLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Non authentifié']);
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT amount FROM access_codes WHERE code = ?");
$stmt->execute([$_SESSION['player_code']]);
$codeData = $stmt->fetch();

echo json_encode([
    'success' => true,
    'balance' => floatval($codeData['amount']),
    'code' => $_SESSION['player_code']
]);
