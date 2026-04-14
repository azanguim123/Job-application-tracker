<?php 

// iclusion de la base de donnee 
 require_once "config/database.php";

 // demarage de session 
session_start();

// Initialiser le tableau d'erreur
 $errors = [];

 // Verifier si la request est de type POST
 if ($_SERVER['REQUEST_METHOD'] === 'POST'){
      $submitted = true;
    //Recuperer et nettoyer les donnees du formulaire
    $email = trim($_POST['email']?? '');
    $password =  $_POST['password'] ?? '';

    // Valider l'email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] =  "L'email est invalide ou vide. ";
    }

    // Valider le mot de passe
    if (empty($password)){
        $errors[] = "Le mot de passe est obligatoire. "; 
    }

    // Si aucune erreur, verifier les identifiants

    if (empty($errors)){
        try{
            // chercher l'utilisateur par mail
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch();

            //Verifier si l'utilisateur existe et si le mot de passe est correct
            if ($user && password_verify($password, $user['password'])){
                // Creer les variables de session 
                $_SESSION['user_id'] =  $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_fullname'] =  $user['full_name'];


                // Rediger vers une page protegee (dashboard.php)
                header("Location: dashboard.php");
                exit();
            } else {
                $errors [] =  "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e){
            $errors[] = "Erreur de base de donnees: " . $e->getMessage();
        }


    }
 }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
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
            max-width: 450px;
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
            animation-delay: 0.3s;
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

        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            font-size: 14px;
            color: #666;
            animation: fadeInUp 0.6s ease-out;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .demo-info {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
            animation-delay: 0.5s;
            animation-fill-mode: both;
        }

        .demo-info p {
            margin: 5px 0;
        }

        .demo-info strong {
            color: #667eea;
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
    <h1>Connexion</h1>
     <div class="subtitle">Suivi de vos candidatures</div>

    <!-- Afficher les erreurs si elles existent -->
    <?php if ($submitted && !empty($errors)): ?>
        <div class="errors-box">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="POST" action="login.php">
        
        <div class="input-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" 
                       placeholder="••••••••" required>
        </div>
        
        <button type="submit">Se connecter</button>
    </form>
     
     <div class="register-link">
            Pas encore de compte ? <a href="register.php">Créer un compte</a>
     </div>
     
     <div class="demo-info">
            <p><strong> Informations de démonstration :</strong></p>
            <p>Email: demo@example.com<br>Mot de passe: demo123</p>
        </div>
     
  </div>
</body>
</html>



