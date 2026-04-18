<?php
require_once 'config.php';

$error = '';
$success = '';

// Traitement de la soumission du code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_code'])) {
    $accessCode = trim($_POST['access_code']);
    
    if (empty($accessCode)) {
        $error = 'Veuillez entrer un code d\'accès';
    } else {
        $db = getDB();
        
        // Vérifier le code dans la base de données
        $stmt = $db->prepare("SELECT * FROM access_codes WHERE code = ? AND is_used = FALSE AND (expires_at IS NULL OR expires_at > NOW())");
        $stmt->execute([$accessCode]);
        $codeData = $stmt->fetch();
        
        if ($codeData) {
            // Code valide, connecter le joueur
            $_SESSION['player_code'] = $codeData['code'];
            $_SESSION['player_amount'] = $codeData['amount'];
            $_SESSION['code_id'] = $codeData['id'];
            
            // Marquer le code comme utilisé
            $updateStmt = $db->prepare("UPDATE access_codes SET is_used = TRUE, used_at = NOW(), player_ip = ? WHERE id = ?");
            $updateStmt->execute([$_SERVER['REMOTE_ADDR'], $codeData['id']]);
            
            $success = 'Code accepté ! Redirection vers les jeux...';
            header("Refresh: 2; url=dashboard.php");
        } else {
            $error = 'Code invalide, déjà utilisé ou expiré';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Accès Jeux Casino</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 4rem;
            color: #00ff88;
            margin-bottom: 15px;
        }
        
        .logo h1 {
            font-size: 2.5rem;
            background: linear-gradient(45deg, #00ff88, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .logo p {
            color: #aaa;
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #00ccff;
            font-weight: 600;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #00ff88;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(0, 204, 255, 0.3);
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            transition: all 0.3s;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #00ff88, #00ccff);
            border: none;
            border-radius: 10px;
            color: #1a1a2e;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 255, 136, 0.4);
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-success {
            background: rgba(0, 255, 136, 0.2);
            border: 1px solid #00ff88;
            color: #00ff88;
        }
        
        .alert-error {
            background: rgba(255, 0, 68, 0.2);
            border: 1px solid #ff0044;
            color: #ff0044;
        }
        
        .admin-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-link a {
            color: #00ccff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .admin-link a:hover {
            text-decoration: underline;
        }
        
        .game-info {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .game-item {
            text-align: center;
        }
        
        .game-item i {
            font-size: 2rem;
            color: #00ccff;
            margin-bottom: 10px;
        }
        
        .game-item span {
            display: block;
            font-size: 0.9rem;
            color: #aaa;
        }
        
        @media (max-width: 600px) {
            .card {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <i class="fas fa-dice"></i>
                <h1>CASINO ACCESS</h1>
                <p>Entrez votre code pour accéder aux jeux</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="access_code"><i class="fas fa-key"></i> CODE D'ACCÈS</label>
                    <div class="input-with-icon">
                        <i class="fas fa-ticket-alt"></i>
                        <input type="text" 
                               id="access_code" 
                               name="access_code" 
                               placeholder="Ex: CAS-AB12CD34EF56" 
                               required
                               autocomplete="off"
                               autofocus>
                    </div>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-gamepad"></i> ACCÉDER AUX JEUX
                </button>
            </form>
            
            <div class="game-info">
                <div class="game-item">
                    <i class="fas fa-dice-d20"></i>
                    <span>KENO</span>
                </div>
                <div class="game-item">
                    <i class="fas fa-sliders-h"></i>
                    <span>SLOTS</span>
                </div>
                <div class="game-item">
                    <i class="fas fa-coins"></i>
                    <span>CRÉDITS</span>
                </div>
            </div>
            
            <div class="admin-link">
                <a href="admin/login.php">
                    <i class="fas fa-user-shield"></i> Espace Administrateur
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s, transform 0.5s';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
            
            // Focus sur le champ de code
            document.getElementById('access_code').focus();
        });
    </script>
</body>
</html>
