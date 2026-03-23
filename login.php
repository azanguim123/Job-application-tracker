<?php 

// iclusion de la base de donnee 
 require_once "config/database.php";

 // demarage de session 
session_start();

// Initialiser le tableau d'erreur
 $errors = [];

 // Verifier si la request est de type POST
 if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    //Recuperer et nettoyer les donnees du formulaire
    $email = trim($_POST['email']?? '');
    $passwrod =  trim($_POST['password'] ?? '');

    // Valider l'email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors[] =  "L'email est invalide ou vide. ";
    }

    // Valider le mot de passe
    if (empty($passwrod)){
        $errors[] = "Le mot de passe est obligatoire. "; 
    }

    // Si aucune erreur, verifier les identifiants

    if (empty($errors)){
        try{
            // chercher l'utilisateur par mail
            $stml = $pdo->prepare("SELECT * FROM job-tracker WHERE email = :email");
            $stml->bindParam(':email', $email);
            $stml->execute();

            $users = $stml->fetch();


            //Verifier si l'utilisateur existe et si le mot de passe est correct
            if ($users && password_verify($password, $users['password'])){
                // Creer les variables de session 
                $_SESSION['users_id'] =  $users['id'];
                $_SEESION['users_email'] = $users['email'];
                $_SEESION['users_fullname'] =  $users ['full_name'];


                // Rediger ves une page protegee (dashboard.php)
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