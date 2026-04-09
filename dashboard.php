<?php
// Include le fichier de connexion a la base de donnees
require_once 'config/database.php';

// Demarrer la session
session_start();

// Verifier si l'utilisateur est connecte 
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Recupere les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$user_full_name = htmlspecialchars($_SESSION['user_fullname'] ?? 'Utilisateur');
$user_email = htmlspecialchars($_SESSION['user_email'] ?? '');

// Recuperer les candidatures de l'utilisateur connecte
try {
    $stmt = $pdo->prepare("
    SELECT id, company_name, job_title, location, status, application_date, notes
    FROM applications
    WHERE user_id = :user_id
    ORDER BY  application_date DESC
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e){
    die("Erreur lors de la recuperation des candidatures: " .$e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mes Candidatures</h1>
            <div>
                <p>Bonjour, <?php echo $user_nom; ?></p>
                <a href="logout.php" class="btn">Se déconnecter</a>
            </div>
        </div>

        <a href="create_application.php" class="btn">Ajouter une candidature</a>

        <!-- Tableau des candidatures -->
        <?php if (empty($applications)): ?>
            <p>Aucune candidature enregistrée.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Entreprise</th>
                        <th>Poste</th>
                        <th>Localisation</th>
                        <th>Statut</th>
                        <th>Date de Candidature</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                            <td><?php echo htmlspecialchars($app['location']); ?></td>
                            <td><?php echo htmlspecialchars($app['status']); ?></td>
                            <td><?php echo htmlspecialchars($app['application_date']); ?></td>
                            <td><?php echo htmlspecialchars($app['notes']); ?></td>
                            <td>
                                <a href="delete_application.php?id=<?php echo $app['id']; ?>" 
                                onclick="return confirm('Are you sure?')"> 
                                Supprimer 
                                </a>
                            </td>
                            <td><?php echo $app['id']; ?></td>
                        </tr>
                        
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>