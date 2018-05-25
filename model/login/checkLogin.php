<?php
	session_start();
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$loginUsername = '';
	$loginPassword = '';
	$hashedPassword = '';
	
	if(isset($_POST['loginUsername'])){
		$loginUsername = $_POST['loginUsername'];
		$loginPassword = $_POST['loginPassword'];
		
		if(!empty($loginUsername) && !empty($loginUsername)){
			
			// Sanitize username
			$loginUsername = filter_var($loginUsername, FILTER_SANITIZE_STRING);
			
			// Check if username is empty
			if($loginUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Username</div>';
				exit();
			}
			
			// Check if password is empty
			if($loginPassword == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Password</div>';
				exit();
			}
			
			// Encrypt the password
			$hashedPassword = md5($loginPassword);
			
			// Check the given credentials
			$checkUserSql = 'SELECT * FROM user WHERE username = :username AND password = :password';
			$checkUserStatement = $conn->prepare($checkUserSql);
			$checkUserStatement->execute(['username' => $loginUsername, 'password' => $hashedPassword]);
			
			// Check if user exists or not
			if($checkUserStatement->rowCount() > 0){
				// Valid credentials. Hence, start the session
				$row = $checkUserStatement->fetch(PDO::FETCH_ASSOC);

				$_SESSION['loggedIn'] = '1';
				$_SESSION['fullName'] = $row['fullName'];
				
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Login success! Redirecting you to home page...</div>';
				exit();
			} else {
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Incorrect Username / Password</div>';
				exit();
			}
			
			
		} else {
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Username and Password</div>';
			exit();
		}
	}
?>