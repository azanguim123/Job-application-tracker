<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Demarrer la session 
session_start();

// Inclure le fichier de connexion a la base d donnees
require_once 'config/database.php';

// Verifier si l'utilisateur est connecte

if(!isset($_SESSION['user_id'])){
    header("Location: dashboard.php");
	exit();
}
$user_id = $_SESSION['user_id'];

// Initialiser le tableau d'erreurs
$errors = [];

// Verifier si la requete est de type POST
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	// Recuperer et nettoyer les donnes du formulaire
	$company_name = trim($_POST['company_name'] ?? '');
	$job_title = trim($_POST['job_title'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$status = trim($_POST['status'] ?? '');
	$application_date = trim($_POST['application_date'] ?? '');
	$notes = trim($_POST['notes'] ?? '');

	//Valider les champs
	if(empty($company_name)) {
		$errors [] = "Le nom de l'entreprise est obligatoire.";
	}
	if (empty($job_title)) {
		$errors[] = "Le poste est obligatoire.";
	}
	if(empty($location)){
		$errors[] = "La localisation est obligatoire.";
	}
	if(empty($status)) {
		$errors[] = "Le status est obligatoire.";
	}
	if (empty($application_date)) {
		$errors[] = "La date de candidature est obligatoire.";
	}


// Si aucune erreur, inserer les donnees en base de donnees

	if (empty($errors)) {
		try {
			// Preparer la requete SQL
			$stmt = $pdo->prepare("
				INSERT INTO applications 
				(user_id, company_name, job_title, location, status, application_date, notes)
				VALUES (:user_id, :company_name, :job_title, :location, :status, :application_date, :notes)
				");

			// Lier les parametres
			$stmt->bindParam(':user_id', $user_id);
			$stmt->bindParam(':company_name', $company_name);
			$stmt->bindParam(':job_title', $job_title);
			$stmt->bindParam(':location', $location);
			$stmt->bindParam(':status', $status);
			$stmt->bindParam(':application_date', $application_date);
			$stmt->bindParam(':notes', $notes);

			// Executer la requete
			$stmt->execute();

			// Rediriger vers une page de confirmation ou de liste des candidatures
			header("Location: dashboard.php");
			exit();
		} catch (PDOException $e) {
			$errors[] = "Erreur lors de l'enregistrement : ". $e->getMessage();
		}
	}
}
  
?>


<!DOCTYPE html>
<html lang = "fr">
<head>
    <meta charset = "UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Nouvelle Candidature </title>

    <style>
    	
    	body {
    		font-family: Arial, sans-serif;
    		background-color: #f4f4f4;
    		display: flex;
    		justify-content: center;
    		align-items: center;
    		min-height: 100vh;
    		margin: 0;
    	}
    	.form-container {
    		background: #fff;
    		padding: 20px;
    		border-radius: 5px;
    		box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    		width: 500px;
    	}

    	.form-container h1 {
    		text-align: center;
    		margin-bottom: 20px;
    	}

    	.form-group {
    		margin-bottom: 15px;
    	}
    	.form-group label {
    		display: block;
    		margin-bottom: 5px;
    		font-weight: bold;
    	}
    	.form-group input, 
    	.form-group select,
    	.form-group textarea {
    		width: 100%;
    		padding: 8px;
    		border: 1px solid #ddd;
    		border-radius: 4px;
    		box-sizing: border-box;
    	}
    	.form-group textarea{
    		height: 100px;
    		resize: vertical;
    	}
    	.form-group button {
    		width: 100%;
    		padding: 10px;
    		background-color: #28a745;
    		color: #fff;
    		border: none;
    		border-radius: 4px;
    		cursor: pointer;
    	}
    	.form-group button:hover {
    		background-color: #218838;
    	}
    	.errors {
    		color: #dc3545;
    		margin-bottom: 15px;
    	}

    </style>

</head>
<body>
    <div class= "form-container">
        <h1>Ajouter une Candidature</h1>

        <!-- Afficher les erreurs (si elle existent) -->
        <?php if(!empty($errors)): ?>
        	<div class="errors">
        		<?php foreach ($errors as $error): ?> 
        			<p><?php echo htmlspecialchars($error); ?></p>
        		<?php endforeach; ?>
        	</div>
        <?php endif; ?>

        <!-- Formulaire de candidature -->
        <form method="POST" action="create_application.php">
        	<div class="form-group">
        		<label for="company_name"> Nom de l'entreprise :</label>
        		<input type="text" id="company_name" name="company_name" required>
        	</div>
        	<div class="form-group">
        		<label for="job_title"> Poste : </label>
        		<input type="text" id="job_title" name="job_title" required>
        	</div>	
        	<div class="form-group">
        		<label for="location"> Localisation :</label>
        		<input type="text" id="location" name="location" required>
        	</div>
        	<div class="form-group">
        		<label for="status"> Statut :</label>
        		<select id="status" name="status" required>
        			<option value=""> -- Selectionnez un status --></option>
        			<option value="Applied">Applied</option>
        			<option value="Interview">Interview</option>
        			<option value="Rejected">Rejected</option>
        			<option value="Accepted">Accepted</option>	
        		</select>
        	</div>
			<div class="form-group">
        		<label for="application_date">Date de candidature :</label>
        		<input type="date" id="application_date" name="application_date" required>	
        	</div>
        	<div class="form-group">
        		<label for="notes">Notes :</label>
        		<textarea id="notes" name="notes"></textarea>	
        	</div>
        	<div class="form-group">
        		<button type="submit">Enregistrer</button>	
        	</div>
       </form>
    </div>
</body>

 </html>      		