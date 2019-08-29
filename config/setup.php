<?php
include_once("database.php");
include_once("../model/Bdd.php");
$pdo = new Bdd($DB_DSN, $DB_USER, $DB_PASSWORD);
try {
	$pdo->query("use matcha;");
	$pdo->query("SELECT * FROM users");
}
catch (Exception $e){
	$pdo->init_bdd();
}
?>
