<?php

$dsn = "mysql:host=localhost;dbname=mysitedb";
$dbusername = "root";
$dbpassword ="";

try {
    $pdo = new PDO($dsn, $dbusername, $dbpassword);
    $pdo -> SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    echo "connection failed! " . $e->getMessage();
}