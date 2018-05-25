<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');

	// Execute the script if the POST request is submitted
	if(isset($_POST['purchaseDetailsPurchaseID'])){
		
		$purchaseID = htmlentities($_POST['purchaseDetailsPurchaseID']);
		
		$purchaseDetailsSql = 'SELECT * FROM purchase WHERE purchaseID = :purchaseID';
		$purchaseDetailsStatement = $conn->prepare($purchaseDetailsSql);
		$purchaseDetailsStatement->execute(['purchaseID' => $purchaseID]);
		
		// If data is found for the given purchaseID, return it as a json object
		if($purchaseDetailsStatement->rowCount() > 0) {
			$row = $purchaseDetailsStatement->fetch(PDO::FETCH_ASSOC);
			echo json_encode($row);
		}
		$purchaseDetailsStatement->closeCursor();
	}
?>