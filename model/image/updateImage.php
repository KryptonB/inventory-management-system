<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['itemImageItemNumber'])){
		
		$itemImageItemNumber = htmlentities($_POST['itemImageItemNumber']);
		
		$baseImageFolder = '../../data/item_images/';
		$itemImageFolder = '';
		
		if(!empty($itemImageItemNumber)){
			
			// Check if the user has selected an image
			if($_FILES['itemImageFile']['name'] != ''){
				// Both itemNumber and image file given. Hence, proceed to next steps
				
				// Sanitize item number
				$itemImageItemNumber = filter_var($itemImageItemNumber, FILTER_SANITIZE_STRING);
				
				// Check if itemNumber is in DB
				$itemNumberSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
				$itemNumberStatement = $conn->prepare($itemNumberSql);
				$itemNumberStatement->execute(['itemNumber' => $itemImageItemNumber]);
				
				if($itemNumberStatement->rowCount() > 0){
					// Item is in the DB, hence proceed to next steps
					// Check the file .extension
					$arr = explode('.', $_FILES['itemImageFile']['name']);
					$extension = strtolower(end($arr));
					$allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
					
					if(in_array($extension, $allowedTypes)){
						// All good so far...
						
						$baseImageFolder = '../../data/item_images/';
						$itemImageFolder = '';
						$fileName = time() . '_' . basename($_FILES['itemImageFile']['name']);
						
						// Create image folder for uploading images
						$itemImageFolder = $baseImageFolder . $itemImageItemNumber . '/';
						if(is_dir($itemImageFolder)){
							// Folder already exists. Hence, do nothing
						} else {
							// Folder does not exist, Hence, create it
							mkdir($itemImageFolder);
						}
						
						$targetPath = $itemImageFolder . $fileName;
						//echo $targetPath;
						//exit();
						
						// Upload file to server
						if(move_uploaded_file($_FILES['itemImageFile']['tmp_name'], $targetPath)){
							
							// Update image url in item table
							$updateImageUrlSql = 'UPDATE item SET imageURL = :imageURL WHERE itemNumber = :itemNumber';
							$updateImageUrlStatement = $conn->prepare($updateImageUrlSql);
							$updateImageUrlStatement->execute(['imageURL' => $fileName, 'itemNumber' => $itemImageItemNumber]);
							
							echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Image uploaded successfully.</div>';
							exit();
							
						} else {
							echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Could not upload image.</div>';
							exit();
						}
						
					} else {
					// Image type is not allowed
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Image type is not allowed. Please select a valid image.</div>';
					exit();
					}
				}
				
			} else {
				// Image file not given
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please select an image</div>';
				exit();
			}
		
		} else {
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter item number</div>';
			exit();
		}

	}

?>