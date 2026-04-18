<?php
require_once 'config.php';

// Vérifier si le joueur est connecté
if (!isPlayerLoggedIn()) {
    redirect('index.php');
}

$db = getDB();
$playerCode = $_SESSION['player_code'];
$playerAmount = $_SESSION['player_amount'];

// Récupérer les informations du code
$stmt = $db->prepare("SELECT * FROM access_codes WHERE code = ?");
$stmt->execute([$playerCode]);
$codeInfo = $stmt->fetch();

// Récupérer l'historique des transactions
$transactionsStmt = $db->prepare("
    SELECT * FROM transactions 
    WHERE code_id = ? 
    ORDER BY transaction_time DESC 
    LIMIT 10
");
$transactionsStmt->execute([$codeInfo['id']]);
$transactions = $transactionsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            color: white;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 25px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid rgba(0, 204, 255, 0.2);
        }
        
        .user-details h2 {
            color: #00ccff;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        
        .user-details p {
            color: #aaa;
            font-size: 1.1rem;
        }
        
        .balance {
            text-align: right;
        }
        
        .balance-amount {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00ff88, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }
        
        .balance-label {
            color: #aaa;
            font-size: 1rem;
        }
        
        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .game-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .game-card:hover {
            transform: translateY(-5px);
            border-color: #00ff88;
            box-shadow: 0 10px 25px rgba(0, 255, 136, 0.2);
        }
        
        .game-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .keno-icon {
            color: #00ff88;
        }
        
        .slots-icon {
            color: #ffcc00;
        }
        
        .game-card h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: white;
        }
        
        .game-card p {
            color: #aaa;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .play-btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(45deg, #00ff88, #00ccff);
            color: #1a1a2e;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .play-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 255, 136, 0.4);
        }
        
        .history-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .history-section h3 {
            color: #00ccff;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .history-table th {
            text-align: left;
            padding: 15px;
            background: rgba(0, 204, 255, 0.1);
            color: #00ccff;
            border-bottom: 2px solid rgba(0, 204, 255, 0.3);
        }
        
        .history-table td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            color: #ddd;
        }
        
        .win-amount {
            color: #00ff88;
            font-weight: bold;
        }
        
        .bet-amount {
            color: #ffcc00;
        }
        
        .logout-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 12px 25px;
            background: rgba(255, 0, 68, 0.2);
            color: #ff0044;
            border: 1px solid #ff0044;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255, 0, 68, 0.3);
            transform: scale(1.05);
        }
        
        .empty-history {
            text-align: center;
            padding: 40px;
            color: #aaa;
            font-style: italic;
        }
        @media (max-width: 768px) {
            .games-grid {
                grid-template-columns: 1fr;
            }
            
            .user-info {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }
            
            .balance {
                text-align: center;
            }
            
            .history-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1><i class="fas fa-dice"></i> <?php echo SITE_NAME; ?></h1>
        </div>
    </div>
    
    <div class="container">
        <div class="user-info">
            <div class="user-details">
                <h2><i class="fas fa-user"></i> Joueur Connecté</h2>
                <p>Code: <strong><?php echo $playerCode; ?></strong> • Connecté depuis: <?php echo $_SERVER['REMOTE_ADDR']; ?></p>
                <p>Actif depuis: <?php echo date('d/m/Y H:i:s', strtotime($codeInfo['used_at'])); ?></p>
            </div>
            <div class="balance">
                <div class="balance-amount"><?php echo formatMoney($playerAmount); ?></div>
                <div class="balance-label"><i class="fas fa-coins"></i> SOLDE DISPONIBLE</div>
            </div>
        </div>
        
        <div class="games-grid">
            <div class="game-card" onclick="window.location.href='play.php?game=keno'">
                <div class="game-icon keno-icon">
                    <i class="fas fa-dice-d20"></i>
                </div>
                <h3>KENO</h3>
                <p>Jeu de loterie excitant. Choisissez vos nombres et gagnez gros avec des multiplicateurs impressionnants.</p>
                <a href="play.php?game=keno" class="play-btn">
                    <i class="fas fa-play"></i> JOUER AU KENO
                </a>
            </div>
            
            <div class="game-card" onclick="window.location.href='play.php?game=slots'">
                <div class="game-icon slots-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <h3>SLOTS</h3>
                <p>Machine à sous avec 5 rouleaux et 20 lignes de paiement. Tournez les rouleaux et gagnez le jackpot!</p>
                <a href="play.php?game=slots" class="play-btn">
                    <i class="fas fa-play"></i> JOUER AUX SLOTS
                </a>
            </div>
        </div>
        
        <div class="history-section">
            <h3><i class="fas fa-history"></i> HISTORIQUE DES TRANSACTIONS</h3>
            
            <?php if (empty($transactions)): ?>
                <div class="empty-history">
                    <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <p>Aucune transaction pour le moment. Commencez à jouer!</p>
                </div>
            <?php else: ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date/Heure</th>
                            <th>Jeu</th>
                            <th>Mise</th>
                            <th>Gain</th>
                            <th>Résultat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($transaction['transaction_time'])); ?></td>
                                <td>
                                    <?php if ($transaction['game_type'] == 'keno'): ?>
                                        <i class="fas fa-dice-d20"></i> KENO
                                    <?php else: ?>
                                        <i class="fas fa-sliders-h"></i> SLOTS
                                    <?php endif; ?>
                                </td>
                                <td class="bet-amount"><?php echo formatMoney($transaction['bet_amount']); ?></td>
                                <td class="win-amount"><?php echo formatMoney($transaction['win_amount']); ?></td>
                                <td>
                                    <?php if ($transaction['win_amount'] > $transaction['bet_amount']): ?>
                                        <span style="color: #00ff88;"><i class="fas fa-trophy"></i> GAIN</span>
                                    <?php elseif ($transaction['win_amount'] > 0): ?>
                                        <span style="color: #ffcc00;"><i class="fas fa-coins"></i> RÉCUPÉRATION</span>
                                    <?php else: ?>
                                        <span style="color: #ff4444;"><i class="fas fa-times"></i> PERTE</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> DÉCONNEXION
    </a>
    
    <script>
        // Mise à jour en temps réel du solde (simulation)
        function updateBalance() {
            // Dans une vraie application, on ferait une requête AJAX
            console.log('Balance mise à jour');
        }
        
        // Mettre à jour toutes les 30 secondes
        setInterval(updateBalance, 30000);
        
        // Animation des cartes de jeu
        document.addEventListener('DOMContentLoaded', function() {
            const gameCards = document.querySelectorAll('.game-card');
            gameCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s, transform 0.5s';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
        });
    </script>
</body>
</html>
