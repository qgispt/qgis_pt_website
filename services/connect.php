<?php 
	$host = 'localhost';
	$dbname = 'db';
	$user = 'user';
	$pass = 'pass';
	
	$conn = pg_connect("host=$host dbname=$dbname user=$user password=$pass") or die("Não foi possível estabelecer a conexão à base de dados");
?>
