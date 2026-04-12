<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//Demarer la session
session_start();

// Fichier de connextion a la base de donnee
require_once 'config/database.php';

// Verifie si user connecter
if (!isset($_SESSION['user_id'])){
    hearder("Location: login.php");
    exit();
}
// recuperation ID
if (!isset($_GET['id'])){
    die("ID manquant");
}
$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Recuperer la candidature (GET)
$stmt =  $pdo->prepare("
    SELECT *FROM applications 
    WHERE id = :id AND user_id = :user_id  //empeche modification des autres utilisateur
");

$stmt->execute([
    'id' => $id,
    'user_id' => $user_id
]);

$application = $stmt->fetch(PDO::FETCH_ASSOC);

if (!application){
    die("Cadidature introuvable");
}

// Si formulaire soumis (POST)

if ($_SERVER['REQUEST_METHOD']=== 'POST'){
    $company_name = $_POST['company_name'];
    $job_title    = $_POST['job_title'];
    $location     = $_POST['location'];
    $status       = $_POST['status'];
    $application_date = ['application_date'];
    $notes         = $_POST['notes'];

    // Actualiser le formulaire avec les nouvelles informations
    $stmt = $pdo->prepare("
        UPDATE applications
        SET company_name = :company_name,
            job_title = :job_title,
            location = :location,
            status = :status,
            application_date = :application_date, 
            notes = :notes,
        WHERE id = :id AND user_id = :user_id
    ");

    $stmt->execute([
        'company_name' => $company_name,
        'job_title' => $job_title,
        'location' => $location,
        'status' => $status,
        'application_date' => $application_date,
        'notes' => $notes,
        'id' => $id,
        'user_id' => $user_id
    ]);

    header("Location: dashboard.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une candidature</title>
</head>

<body>

    <h1>Modifier une candidature</h1>

    <form method="POST">

        <input type="text" name="company_name"
        value="<?php echo htmlspecialchars($application['company_name']); ?>">

        <input type="text" name="job_title"
        value="<?php echo htmlspecialchars($application['job_title']); ?>">

        <input type="text" name="location"
        value="<?php echo htmlspecialchars($application['location']); ?>">

        <select name="status">
            <option value="Applied" <?php if($application['status']=="Applied") echo "selected"; ?>>Applied</option>
            <option value="Interview" <?php if($application['status']=="Interview") echo "selected"; ?>>Interview</option>
            <option value="Rejected" <?php if($application['status']=="Rejected") echo "selected"; ?>>Rejected</option>
            <option value="Accepted" <?php if($application['status']=="Accepted") echo "selected"; ?>>Accepted</option>
        </select>

        <input type="date" name="application_date"
        value="<?php echo $application['application_date']; ?>">

        <textarea name="notes"><?php echo htmlspecialchars($application['notes']); ?></textarea>

        <button type="submit">Mettre à jour</button>

    </form>

</body>
</html>