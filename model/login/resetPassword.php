<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$resetPasswordUsername = '';
	$resetPasswordPassword1 = '';
	$resetPasswordPassword2 = '';
	$hashedPassword = '';
	
	if(isset($_POST['resetPasswordUsername'])){
		$resetPasswordUsername = htmlentities($_POST['resetPasswordUsername']);
		$resetPasswordPassword1 = htmlentities($_POST['resetPasswordPassword1']);
		$resetPasswordPassword2 = htmlentities($_POST['resetPasswordPassword2']);
		
		if(!empty($resetPasswordUsername) && !empty($resetPasswordPassword1) && !empty($resetPasswordPassword2)){
			
			// Check if username is empty
			if($resetPasswordUsername == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter your username.</div>';
				exit();
			}
			
			// Check if passwords are empty
			if($resetPasswordPassword1 == '' || $resetPasswordPassword2 == ''){
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter both passwords.</div>';
				exit();
			}
			
			// Check if username is available
			$usernameCheckingSql = 'SELECT * FROM user WHERE username = :username';
			$usernameCheckingStatement = $conn->prepare($usernameCheckingSql);
			$usernameCheckingStatement->execute(['username' => $resetPasswordUsername]);
			
			if($usernameCheckingStatement->rowCount() < 1){
				// Username doesn't exist. Hence can't reset password
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Username does not exist.</div>';
				exit();
			} else {
				// Check if passwords are equal
				if($resetPasswordPassword1 !== $resetPasswordPassword2){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Passwords do not match.</div>';
					exit();
				} else {
					// Start UPDATING password to DB
					// Encrypt the password
					$hashedPassword = md5($resetPasswordPassword1);
					$updatePasswordSql = 'UPDATE user SET password = :password WHERE username = :username';
					$updatePasswordStatement = $conn->prepare($updatePasswordSql);
					$updatePasswordStatement->execute(['password' => $hashedPassword, 'username' => $resetPasswordUsername]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Password reset complete. Please login using your new password.</div>';
					exit();
				}
			}
		} else {
			// One or more mandatory fields are empty. Therefore, display a the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter all fields marked with a (*)</div>';
			exit();
		}
	}
?>