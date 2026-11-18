<?php
	// Require an authenticated session before any data endpoint runs.
	if(session_status() === PHP_SESSION_NONE){
		session_start();
	}
	if(!isset($_SESSION['loggedIn'])){
		http_response_code(401);
		exit();
	}
?>