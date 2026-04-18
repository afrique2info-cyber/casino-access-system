<?php
require_once '../config.php';

// Vérifier si admin est connecté
if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$db = getDB();

// Statistiques (calcul direct sans vue)
$statsQuery = $db->query("
    SELECT 
        COUNT(*) as total_codes,
        SUM(CASE WHEN is_used = FALSE THEN 1 ELSE 0 END) as available_codes,
        SUM(CASE WHEN is_used = TRUE THEN 1 ELSE 0 END) as used_codes,
        SUM(amount) as total_amount,
        SUM(CASE WHEN is_used = FALSE THEN amount ELSE 0 END) as available_amount
    FROM access_codes
");
$stats = $statsQuery->fetch();

// Codes récents
$recentCodesStmt = $db->query("
    SELECT * FROM access_codes 
    ORDER BY created_at DESC 
    LIMIT 10
");
$recentCodes = $recentCodesStmt->fetchAll();

// Transactions récentes
$recentTransactionsStmt = $db->query("
    SELECT t.*, c.code 
    FROM transactions t
    JOIN access_codes c ON t.code_id = c.id
    ORDER BY t.transaction_time DESC 
    LIMIT 10
");
$recentTransactions = $recentTransactionsStmt->fetchAll();

// Traitement génération de code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_code'])) {
    $amount = floatval($_POST['amount']);
    $expiryDays = intval($_POST['expiry_days']);
    
    if ($amount > 0) {
        $code = generateAccessCode();
        $expiresAt = $expiryDays > 0 ? date('Y-m-d H:i:s', strtotime("+$expiryDays days")) : null;
        
        $stmt = $db->prepare("
            INSERT INTO access_codes (code, amount, expires_at) 
            VALUES (?, ?, ?)
        ");
        
        if ($stmt->execute([$code, $amount, $expiresAt])) {
            $_SESSION['success_message'] = "Code généré: $code - Montant: " . formatMoney($amount);
        } else {
            $_SESSION['error_message'] = "Erreur lors de la génération du code";
        }
        
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --admin-primary: #ff0044;
            --admin-secondary: #ff6600;
            --admin-dark: #1a1a2e;
            --admin-light: #2d2d44;
        }
        
        body {
            background: var(--admin-dark);
            color: white;
            min-height: 100vh;
        }
        
        .admin-header {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 0, 68, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 {
            background: linear-gradient(45deg, var(--admin-primary), var(--admin-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 1.8rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logout-btn {
            padding: 8px 20px;
            background: rgba(255, 0, 68, 0.2);
            color: var(--admin-primary);
            border: 1px solid var(--admin-primary);
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 0, 68, 0.3);
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            border-color: var(--admin-primary);
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--admin-primary);
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 5px;
            background: linear-gradient(45deg, var(--admin-primary), var(--admin-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            color: #aaa;
            font-size: 0.9rem;
        }
        
        .admin-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 1100px) {
            .admin-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .admin-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .section-header h2 {
            color: var(--admin-primary);
            font-size: 1.5rem;
        }
        
        .generate-form {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--admin-secondary);
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 0, 68, 0.3);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--admin-secondary);
        }
        
        .generate-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, var(--admin-primary), var(--admin-secondary));
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .generate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 0, 68, 0.3);
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th {
            text-align: left;
            padding: 15px;
            background: rgba(255, 0, 68, 0.1);
            color: var(--admin-primary);
            border-bottom: 2px solid rgba(255, 0, 68, 0.3);
        }
        
        .admin-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #ddd;
        }
        
        .code-cell {
            font-family: monospace;
            font-weight: bold;
            color: #00ff88;
        }
        
        .amount-cell {
            color: #ffcc00;
            font-weight: bold;
        }
        
        .status-used {
            color: #ff4444;
        }
        
        .status-available {
            color: #00ff88;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .alert-success {
            background: rgba(0, 255, 136, 0.2);
            border: 1px solid #00ff88;
            color: #00ff88;
        }
        
        .alert-error {
            background: rgba(255, 0, 68, 0.2);
            border: 1px solid var(--admin-primary);
            color: var(--admin-primary);
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #aaa;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1><i class="fas fa-user-shield"></i> ADMIN DASHBOARD</h1>
        <div class="user-info">
            <span>Connecté en tant que: <strong><?php echo $_SESSION['admin_username']; ?></strong></span>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
    
    <div class="admin-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-value"><?php echo $stats['total_codes'] ?? 0; ?></div>
                <div class="stat-label">CODES GÉNÉRÉS</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-value"><?php echo formatMoney($stats['total_amount'] ?? 0); ?></div>
                <div class="stat-label">MONTANT TOTAL</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $stats['available_codes'] ?? 0; ?></div>
                <div class="stat-label">CODES DISPONIBLES</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="stat-value"><?php echo $stats['used_codes'] ?? 0; ?></div>
                <div class="stat-label">CODES UTILISÉS</div>
            </div>
        </div>
        
        <div class="admin-grid">
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-plus-circle"></i> GÉNÉRER UN CODE</h2>
                </div>
                
                <form method="POST" action="" class="generate-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="amount"><i class="fas fa-euro-sign"></i> MONTANT (€)</label>
                            <input type="number" 
                                   id="amount" 
                                   name="amount" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="1" 
                                   max="10000" 
                                   value="100" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="expiry_days"><i class="fas fa-calendar-times"></i> EXPIRATION (jours)</label>
                            <select id="expiry_days" name="expiry_days" class="form-control">
                                <option value="0">Pas d'expiration</option>
                                <option value="1">1 jour</option>
                                <option value="7" selected>7 jours</option>
                                <option value="30">30 jours</option>
                                <option value="90">90 jours</option>
                            </select>
                        </div>
                    </div>
                    
                    <button type="submit" name="generate_code" class="generate-btn">
                        <i class="fas fa-magic"></i> GÉNÉRER LE CODE
                    </button>
                </form>
            </div>
            
            <div class="admin-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> CODES RÉCENTS</h2>
                </div>
                
                <div class="table-container">
                    <?php if (empty($recentCodes)): ?>
                        <div class="empty-message">
                            <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                            <p>Aucun code généré pour le moment</p>
                        </div>
                    <?php else: ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>CODE</th>
                                    <th>MONTANT</th>
                                    <th>STATUT</th>
                                    <th>CRÉÉ LE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentCodes as $code): ?>
                                    <tr>
                                        <td class="code-cell"><?php echo $code['code']; ?></td>
                                        <td class="amount-cell"><?php echo formatMoney($code['amount']); ?></td>
                                        <td>
                                            <?php if ($code['is_used']): ?>
                                                <span class="status-used">
                                                    <i class="fas fa-times-circle"></i> Utilisé
                                                </span>
                                            <?php else: ?>
                                                <span class="status-available">
                                                    <i class="fas fa-check-circle"></i> Disponible
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($code['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="admin-section">
            <div class="section-header">
                <h2><i class="fas fa-exchange-alt"></i> TRANSACTIONS RÉCENTES</h2>
            </div>
            
            <div class="table-container">
                <?php if (empty($recentTransactions)): ?>
                    <div class="empty-message">
                        <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                        <p>Aucune transaction pour le moment</p>
                    </div>
                <?php else: ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>CODE</th>
                                <th>JEU</th>
                                <th>MISE</th>
                                <th>GAIN</th>
                                <th>DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTransactions as $transaction): ?>
                                <tr>
                                    <td class="code-cell"><?php echo $transaction['code']; ?></td>
                                    <td>
                                        <?php if ($transaction['game_type'] == 'keno'): ?>
                                            <i class="fas fa-dice-d20"></i> KENO
                                        <?php else: ?>
                                            <i class="fas fa-sliders-h"></i> SLOTS
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount-cell"><?php echo formatMoney($transaction['bet_amount']); ?></td>
                                    <td style="color: <?php echo $transaction['win_amount'] > 0 ? '#00ff88' : '#ff4444'; ?>">
                                        <?php echo formatMoney($transaction['win_amount']); ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($transaction['transaction_time'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Animation des statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s, transform 0.5s';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
            
            // Auto-refresh toutes les 30 secondes
            setInterval(() => {
                location.reload();
            }, 30000);
        });
    </script>
</body>
</html>
