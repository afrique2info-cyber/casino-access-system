<?php
require_once 'config.php';

// Vérifier si le joueur est connecté
if (!isPlayerLoggedIn()) {
    redirect('index.php');
}

$game = $_GET['game'] ?? '';
$allowedGames = ['keno', 'slots'];

if (!in_array($game, $allowedGames)) {
    redirect('dashboard.php');
}

// Récupérer le solde du joueur
$db = getDB();
$stmt = $db->prepare("SELECT amount FROM access_codes WHERE code = ?");
$stmt->execute([$_SESSION['player_code']]);
$codeData = $stmt->fetch();
$playerAmount = $codeData['amount'];

// Déterminer l'URL du jeu
if ($game === 'keno') {
    $gameUrl = KENO_GAME_URL;
    $gameName = 'KENO';
    $gameIcon = 'fas fa-dice-d20';
} else {
    $gameUrl = SLOTS_GAME_URL;
    $gameName = 'SLOTS';
    $gameIcon = 'fas fa-sliders-h';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jouer à <?php echo $gameName; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #1a1a2e;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .game-header {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .game-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .game-icon {
            font-size: 2rem;
            color: #00ff88;
        }
        
        .game-title h1 {
            font-size: 1.8rem;
            color: #00ccff;
        }
        
        .player-balance {
            background: rgba(0, 255, 136, 0.1);
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid #00ff88;
        }
        
        .balance-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #00ff88;
        }
        
        .balance-label {
            font-size: 0.9rem;
            color: #aaa;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        
        .nav-btn {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .game-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }
        
        .game-frame-container {
            flex: 1;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            overflow: hidden;
            border: 2px solid rgba(0, 204, 255, 0.3);
        }
        
        .game-frame {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .game-instructions {
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .instructions-title {
            color: #00ccff;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .instructions-list {
            padding-left: 20px;
            color: #ddd;
        }
        
        .instructions-list li {
            margin-bottom: 8px;
        }
        
        @media (max-width: 768px) {
            .game-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .game-info {
                flex-direction: column;
                gap: 10px;
            }
            
            .nav-buttons {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="game-header">
        <div class="game-info">
            <div class="game-icon">
                <i class="<?php echo $gameIcon; ?>"></i>
            </div>
            <div class="game-title">
                <h1><?php echo $gameName; ?></h1>
                <p>Code: <?php echo $_SESSION['player_code']; ?></p>
            </div>
        </div>
        
        <div class="player-balance">
            <div class="balance-amount"><?php echo number_format($playerAmount, 2, ',', ' '); ?> €</div>
            <div class="balance-label">SOLDE DISPONIBLE</div>
        </div>
        
        <div class="nav-buttons">
            <a href="dashboard.php" class="nav-btn">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="logout.php" class="nav-btn" style="background: rgba(255, 0, 68, 0.2); border-color: #ff0044;">
                <i class="fas fa-sign-out-alt"></i> Quitter
            </a>
        </div>
    </div>
    
    <div class="game-container">
        <div class="game-frame-container">
            <iframe src="<?php echo $gameUrl; ?>" class="game-frame" title="<?php echo $gameName; ?> Game"></iframe>
        </div>
        
        <div class="game-instructions">
            <h3 class="instructions-title">
                <i class="fas fa-info-circle"></i> Instructions du jeu
            </h3>
            <ul class="instructions-list">
                <?php if ($game === 'keno'): ?>
                    <li><strong>KENO</strong> est un jeu de loterie où vous choisissez des nombres</li>
                    <li>Sélectionnez entre 1 et 10 nombres sur la grille de 1 à 80</li>
                    <li>Définissez votre mise et lancez le tirage</li>
                    <li>20 nombres sont tirés au hasard</li>
                    <li>Plus vous avez de nombres correspondants, plus vous gagnez!</li>
                    <li>Les gains sont calculés selon la table des paiements</li>
                <?php else: ?>
                    <li><strong>SLOTS</strong> est une machine à sous avec 5 rouleaux</li>
                    <li>Choisissez votre mise par ligne (jusqu'à 20 lignes)</li>
                    <li>Cliquez sur SPIN pour tourner les rouleaux</li>
                    <li>Les symboles alignés sur les lignes actives donnent des gains</li>
                    <li>Consultez la table des paiements pour connaître les combinaisons gagnantes</li>
                    <li>Le JACKPOT est gagné avec 5 symboles identiques sur une ligne active</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <script>
        // Mettre à jour le solde périodiquement
        function updateBalance() {
            fetch('api/get_balance.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('.balance-amount').textContent = 
                            data.balance.toFixed(2).replace('.', ',') + ' €';
                    }
                })
                .catch(error => console.error('Erreur:', error));
        }
        
        // Mettre à jour toutes les 10 secondes
        setInterval(updateBalance, 10000);
        
        // Redimensionner l'iframe pour s'adapter
        function resizeGameFrame() {
            const frameContainer = document.querySelector('.game-frame-container');
            const headerHeight = document.querySelector('.game-header').offsetHeight;
            const instructionsHeight = document.querySelector('.game-instructions').offsetHeight;
            const containerPadding = 40; // 20px top + 20px bottom
            
            const availableHeight = window.innerHeight - headerHeight - instructionsHeight - containerPadding;
            frameContainer.style.height = Math.max(500, availableHeight) + 'px';
        }
        
        window.addEventListener('resize', resizeGameFrame);
        window.addEventListener('load', resizeGameFrame);
    </script>
</body>
</html>
