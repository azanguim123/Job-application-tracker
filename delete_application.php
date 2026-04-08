<?php
session_start();
require_once 'config/database.php';

// Verifier si utilisateur connecte
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Verifier si id existe dans l'URL 
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try{
        // Supprimer seulement si la candidature appartient a l'utilisateur
        $stmt = $pdo->prepare("
        DELETE FROM applications 
        WHERE id = :id AND user_id = :user_id
        ");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    } catch (PDOException $e){
        die("Erreur supprission : ". $e->getMessage());
    }
}

// Redirection vers dashboard
header("Location: dashboard.php");
exit();