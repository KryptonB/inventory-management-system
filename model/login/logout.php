<?php
	session_start();
	
	unset($_SESSION['loggedIn']);
	unset($_SESSION['fullName']);
	session_destroy();
	header('Location: ../../login.php');
	exit();
?>