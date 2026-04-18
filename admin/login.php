<?php
require_once '../config.php';

// Redirection si déjà connecté
if (isAdminLoggedIn()) {
    redirect('index.php');
}

$error = '';

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        // En production, utiliser password_hash() et password_verify()
        // Pour la démo, on utilise un mot de passe simple
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            redirect('index.php');
        } else {
            $error = 'Identifiants incorrects';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - <?php echo SITE_NAME; ?></title>
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
        
        .admin-login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .admin-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 0, 68, 0.3);
            text-align: center;
        }
        
        .admin-logo {
            margin-bottom: 40px;
        }
        
        .admin-logo i {
            font-size: 4rem;
            color: #ff0044;
            margin-bottom: 20px;
        }
        
        .admin-logo h1 {
            font-size: 2.2rem;
            background: linear-gradient(45deg, #ff0044, #ff6600);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }
        
        .admin-logo p {
            color: #aaa;
            font-size: 1rem;
        }
        
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ff6600;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff0044;
        }
        
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 0, 68, 0.3);
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .input-with-icon input:focus {
            outline: none;
            border-color: #ff6600;
            box-shadow: 0 0 15px rgba(255, 102, 0, 0.3);
        }
        
        .admin-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(45deg, #ff0044, #ff6600);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 0, 68, 0.4);
        }
        
        .alert-error {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            background: rgba(255, 0, 68, 0.2);
            border: 1px solid #ff0044;
            color: #ff0044;
            text-align: center;
        }
        
        .back-link {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .back-link a {
            color: #00ccff;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .demo-credentials {
            margin-top: 25px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 102, 0, 0.3);
            font-size: 0.9rem;
            color: #ffcc00;
        }
        
        .demo-credentials strong {
            color: #ff6600;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-card">
            <div class="admin-logo">
                <i class="fas fa-user-shield"></i>
                <h1>ADMIN PANEL</h1>
                <p><?php echo SITE_NAME; ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> NOM D'UTILISATEUR</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user-cog"></i>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               placeholder="admin" 
                               required
                               autocomplete="off"
                               autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> MOT DE PASSE</label>
                    <div class="input-with-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required>
                    </div>
                </div>
                
                <button type="submit" class="admin-btn">
                    <i class="fas fa-sign-in-alt"></i> SE CONNECTER
                </button>
            </form>
            
            <div class="demo-credentials">
                <i class="fas fa-info-circle"></i> 
                <strong>Identifiants de démo:</strong><br>
                Utilisateur: <strong>admin</strong><br>
                Mot de passe: <strong>admin123</strong>
            </div>
            
            <div class="back-link">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.admin-card');
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s, transform 0.5s';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 100);
        });
    </script>
</body>
</html>
