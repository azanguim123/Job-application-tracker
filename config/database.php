<?php

$host = "sql100.infinityfree.com"; 
$dbname = "if0_41664033_db1";
 $username = "if0_41664033";
 $password = "YVEJAxgB5JrQ";

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