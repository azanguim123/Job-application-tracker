<?php
// Démarrer la session
//session_start();

// Inclure le fichier de configuration PDO
require_once 'config/database.php';

// Initialiser les variables pour les messages d'erreur et de succès
$errors = [];
$success = false;

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des champs
    if (empty($full_name)) {
        $errors[] = "Le nom complet est requis.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Veuillez entrer un email valide.";
    }

    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Si aucune erreur, vérifier si l'email existe déjà
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $errors[] = "Cet email est déjà utilisé.";
            } else {
                // Hacher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insérer l'utilisateur en base de données
                $insert_stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)");
                $insert_stmt->bindParam(':full_name', $full_name);
                $insert_stmt->bindParam(':email', $email);
                $insert_stmt->bindParam(':password', $hashed_password);

                if ($insert_stmt->execute()) {
                    $success = true;
                } else {
                    $errors[] = "Erreur lors de l'inscription.";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur de base de données : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
        <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Effet de particules animées */
        body::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite reverse;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            animation: slideIn 0.5s ease-out;
            position: relative;
            z-index: 1;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card h1 {
            text-align: center;
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .input-group {
            margin-bottom: 25px;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .input-group:nth-child(1) { animation-delay: 0.1s; }
        .input-group:nth-child(2) { animation-delay: 0.2s; }
        .input-group:nth-child(3) { animation-delay: 0.3s; }
        .input-group:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
            animation: fadeInUp 0.6s ease-out;
            animation-delay: 0.5s;
            animation-fill-mode: both;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        .errors-box {
            background: #fee;
            border-left: 4px solid #f44;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: shake 0.5s ease-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .errors-box p {
            color: #d32f2f;
            font-size: 14px;
            margin: 5px 0;
        }

        .success-box {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideIn 0.5s ease-out;
        }

        .success-box p {
            color: #2e7d32;
            font-size: 14px;
            margin: 5px 0;
        }

        .success-box a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .success-box a:hover {
            text-decoration: underline;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            font-size: 14px;
            color: #666;
            animation: fadeInUp 0.6s ease-out;
            animation-delay: 0.6s;
            animation-fill-mode: both;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .card {
                padding: 30px 25px;
            }
            
            .card h1 {
                font-size: 28px;
            }
            
            input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
     <div class="card">
            <h1>Inscription</h1>
            <div class="subtitle">Créez votre compte gratuitement</div>


            <!-- Afficher les messages d'erreur -->
            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Afficher le message de succès -->
            <?php if ($success): ?>
                <div class="success-box">
                    <p>Inscription réussie ! Vous pouvez maintenant vous <a href="login.php">connecter</a>.</p>
                </div>
            <?php else: ?>
         
                <!-- Formulaire d'inscription -->
                <form method="POST" action="register.php">
                    <div>
                        <label for="full_name">Nom complet :</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>"
                               placeholder="Nico Hoffmann"
                               required>
                    </div>
                    
                    <div class="input-group">
                        <label for="email">Email :</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                               placeholder="exemple@email.com"
                               required>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Mot de passe :</label>
                        <input type="password" id="password" name="password" 
                               placeholder="Minimum 6 caractères"
                               required>
                    </div>
                    
                    <div class="input-group">
                        <label for="confirm_password">Confirmer le mot de passe :</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               placeholder="Retapez votre mot de passe"
                               required>
                    </div>
                    
                    <button type="submit">S'inscrire</button>
                </form>
         <div class="login-link">
                Déjà un compte ? <a href="login.php">Se connecter</a>
        </div>
         
            <?php endif; ?>
    </div>
</body>
</html>