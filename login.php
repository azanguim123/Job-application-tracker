<?php 

// iclusion de la base de donnee 
 require_once "config/database.php";

 // demarage de session 
session_start();

// Initialiser le tableau d'erreur
 $errors = [];

 var_dump($password);
var_dump($user['password']);
var_dump(password_verify($password, $user['password']));
var_dump($_POST);

 // Verifier si la request est de type POST
 if ($_SERVER['REQUEST_METHOD'] === 'POST'){
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

            echo "<pre>";
            print_r ($user);
            echo "</pre>";

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
</head>
<body>
    <h1>Connexion</h1>

    <!-- Afficher les erreurs si elles existent -->
    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Formulaire de connexion -->
    <form method="POST" action="login.php">
        <div>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" id="password" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>



