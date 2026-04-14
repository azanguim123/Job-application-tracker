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

// Requete stats
try {
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(status = 'Applied') as applied,
            SUM(status = 'Interview') as interview,
            SUM(status = 'Rejected') as rejected,
            SUM(status = 'Accepted') as accepted
        FROM applications
        WHERE user_id = :user_id"
    );
        $stmt->execute(['user_id' => $user_id]);

    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
die("Erreur stats: " . $e->getMessage());
}

// Recuperer les candidatures de l'utilisateur connecte
try {
    $sql = "
    SELECT id, company_name, job_title, location, status, application_date, notes
    FROM applications
    WHERE user_id = :user_id
    ";

    //  FILTRE 
    if(!empty($_GET['status'])){
        $sql .= " AND status = :status";
    }

    // Tri
    $sql .= " ORDER BY application_date DESC";

    $stmt = $pdo->prepare($sql);

    $params = ['user_id' => $user_id];

    if (!empty($_GET['status'])) {
        $params['status'] = $_GET['status'];
    }

    $stmt->execute($params);
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Tableau de bord</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
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
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }

        /* BUTTONS */
        .btn-delete{
            color: white;
            background-color: red;
            padding: 5px 8px;
            border-radius: 4px;
            text-decoration:none;
        }
        .btn-edit{
            color: white;
            background-color: green;
            padding: 5px 8px;
            border-radius: 4px;
            text-decoration:none;
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
        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .chart-container {
            width: 400px;
            max-width: 100%;
            margin: auto;
        }
        .card {
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-weight: bold;
        }
        .card:nth-child(1){background: #333; color: white; }
        .card:nth-child(2){background: orange; }
        .card:nth-child(3){background: blue; color:white; }
        .card:nth-child(4){background: red; color: white; }
        .card:nth-child(5){background: green; color: white; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
                <p>Bonjour, <?php echo $user_full_name; ?> 👋</p>
                <a class="btn" href="logout.php" >Se déconnecter</a>
            </div>
        </div>

        <!--Filtre de candidatures -->
        <form method="GET">

            <select name="status"> 
                <option value="">Tous</option>
                <option value="Applied" <?php if($_GET['status'] ?? '' == 'Applied') echo 'selected'?>> Applied</option>
                <option value="Interview">Interview</option>
                <option value="Rejected">Rejected</option>
            </select>
            <button type="submit">Filtrer</button>
        </form>

        <br>
            <a class="btn" href="create_application.php" >Ajouter une candidature</a>
        <br><br>

        <!-- Statistique d'applications -->
        <div class="stats">

            <div class="card">Total: <?php echo $stats['total']; ?></div>
            <div class="card">Applied: <?php echo $stats['applied']; ?></div>
            <div class="card">Interview: <?php echo $stats['interview']; ?></div>
            <div class="card">Rejected: <?php echo $stats['rejected']; ?></div>
            <div class="card">Accepted: <?php echo $stats['accepted']; ?></div>
            
        </div>
        <div class="chart-container"> 
            <canvas id="myChart" width="400" height="200"></canvas>
        </div>

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
                            <td class="actions">
                                <a class="btn-delete" href="delete_application.php?id=<?php echo $app['id']; ?>" 
                                onclick="return confirm('Are you sure?')"> 
                                    Supprimer 
                                </a>
                                <a class="btn-edit" href="edit_application.php?id=<?php echo $app['id']; ?>">
                                    Modifier
                                </a>
                            </td>
                            <td><?php echo $app['id']; ?></td>
                        </tr>
                        
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
                    
    // Passage des donnee PHP vers JS
    <script>
        const data = {
            labels: ['Applied', 'Interview', 'Rejected', 'Accepted'],
            datasets : [{
                label: 'Mes candidatures',
                data: [
                    <?php echo $stats['applied']; ?>,
                    <?php echo $stats['interview']; ?>,
                    <?php echo $stats['rejected']; ?>,
                    <?php echo $stats['accepted']; ?>,
                ],
                boderWidth: 1
            }]
        };

        // Creer le graphique
        const config= {
            type: 'pie',
            data: data,
        };
        const myChart = new Chart(
            document.getElementById('myChart'),config
        );
    </script>
</body>
</html>