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
    <title> Nouvelle Candidature - Suivi de Candidatures </title>

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
            padding: 40px 20px;
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

        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 650px;
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

        .form-container h1 {
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

        .form-group {
            margin-bottom: 25px;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.15s; }
        .form-group:nth-child(3) { animation-delay: 0.2s; }
        .form-group:nth-child(4) { animation-delay: 0.25s; }
        .form-group:nth-child(5) { animation-delay: 0.3s; }
        .form-group:nth-child(6) { animation-delay: 0.35s; }
        .form-group:nth-child(7) { animation-delay: 0.4s; }

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

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input, 
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus, 
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group button {
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
        }

        .form-group button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .form-group button:active {
            transform: translateY(0);
        }

        .errors {
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

        .errors p {
            color: #d32f2f;
            font-size: 14px;
            margin: 5px 0;
        }

        .btn-back {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-back:hover {
            background: #e0e0e0;
            transform: translateX(-5px);
        }

        .required:after {
            content: " *";
            color: #f44;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 30px 25px;
            }
            
            .form-container h1 {
                font-size: 28px;
            }
            
            .form-group input, 
            .form-group select,
            .form-group textarea,
            .form-group button {
                font-size: 14px;
            }
        }
    </style>

</head>
<body>
    <div class= "form-container">
        <h1>Ajouter une Candidature </h1>
        <div class="subtitle">Ajoutez une nouvelle candidature à votre suivi</div>

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
                <label for="company_name" class="required"> Nom de l'entreprise</label>
                <input type="text" id="company_name" name="company_name" 
                       placeholder="Ex: Google Germany"
                       value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>"
                       required>
            </div>
            
        	<div class="form-group">
        		<label for="job_title" class="required"> Poste : </label>
        		<input type="text" id="job_title" name="job_title"
                       placeholder="Ex: Développeur PHP Junior"
                 		value="<?php echo htmlspecialchars($_POST['job_title'] ?? ''); ?>"
                       required>
        	</div>
            
        	<div class="form-group">
        		<label for="location" class="required"> Localisation :</label>
        		<input type="text" id="location" name="location" 
                        placeholder="Ex: `Berlin, Germany"
                       value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>"
                       required>
        	</div>
            
        	<div class="form-group">
        		<label for="status" class="required"> Statut :</label>
        		<select id="status" name="status" required>
        			<option value=""> -- Selectionnez un status --></option>
        			<option value="Applied">Applied</option>
        			<option value="Interview">Interview</option>
        			<option value="Rejected">Rejected</option>
        			<option value="Accepted">Accepted</option>	
        		</select>
        	</div>
            
			<div class="form-group">
        		<label for="application_date" class="required">Date de candidature :</label>
        		<input type="date" id="application_date" name="application_date" 
                        value="<?php echo htmlspecialchars($_POST['application_date'] ?? date('Y-m-d')); ?>"
                       required>	
        	</div>
            
        	<div class="form-group">
        		<label for="notes">Notes :</label>
        		<textarea id="notes" name="notes"
                          placeholder="Ajoutez des notes, remarques ou informations complémentaires..."> <?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>	
        	</div>
            
        	<div class="form-group">
        		<button type="submit">Enregistrer la candidature</button>	
        	</div>
            
            <a href="dashboard.php" class="btn-back">← Retour au tableau de bord</a>
            
       </form>
                
    </div>
</body>

 </html>      		