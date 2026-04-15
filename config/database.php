<?php

$host = "localhost";
$dbname = "job_tracker";
 $username = "root";
 $password = "";

try {

	$pdo = new PDO (
		"mysql:host=$host;dbname=$dbname;charset=utf8", 
		$username,
		$password
	);
	$pdo-> setAttribute(PDO:: ATTR_ERRMODE, PDO:: ERRMODE_EXCEPTION);
	//echo "Connexion reussie !";
} catch (PDOException $e) {
	die ("Erreur de connexion :" . $e->getMessage());
}