<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['itemImageItemNumber'])){

			$itemImageItemNumber = htmlentities($_POST['itemImageItemNumber']);
			
			$baseImageFolder = '../../data/item_images/';
			$itemImageFolder = '';
			
			if(!empty($itemImageItemNumber)){
					
				// Sanitize item number
				$itemImageItemNumber = filter_var($itemImageItemNumber, FILTER_SANITIZE_STRING);
				
				// Check if itemNumber is in DB
				$itemNumberSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
				$itemNumberStatement = $conn->prepare($itemNumberSql);
				$itemNumberStatement->execute(['itemNumber' => $itemImageItemNumber]);
				
				if($itemNumberStatement->rowCount() > 0){
					// Item is in the DB, hence proceed to next steps	
					// Update image url in item table to the default image
					$updateImageUrlSql = "UPDATE item SET imageURL = 'imageNotAvailable.jpg' WHERE itemNumber = :itemNumber";
					$updateImageUrlStatement = $conn->prepare($updateImageUrlSql);
					$updateImageUrlStatement->execute(['itemNumber' => $itemImageItemNumber]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Image deleted successfully.</div>';
					exit();
				}
			
			} else {
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter item number</div>';
				exit();
			}

	}

?>