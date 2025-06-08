<?php 

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = $_POST["username"];
	$email = $_POST["email"];
	$password = $_POST["password"];

	try {
		require_once "./dbh.inc.php";

		$query = "MATCH * FROM users (username, pwd, email) VALUES (?,?,?);";

		$stmt =  $pdo->prepare($query);

		$stmt->execute([$username, $password, $email]);

		$pdo = null;
		$stmt = null;
		
		header("Location:../signin.php");

	} catch (PDOException $e) {
		die("Query Failed!" . $e->getMessage());
	}
}else{	
	header("Location:../index.php");
}