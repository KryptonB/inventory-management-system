<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');

	// Execute the script if the POST request is submitted
	if(isset($_POST['saleDetailsSaleID'])){
		
		$saleID = htmlentities($_POST['saleDetailsSaleID']);
		
		$saleDetailsSql = 'SELECT * FROM sale WHERE saleID = :saleID';
		$saleDetailsStatement = $conn->prepare($saleDetailsSql);
		$saleDetailsStatement->execute(['saleID' => $saleID]);
		
		// If data is found for the given saleID, return it as a json object
		if($saleDetailsStatement->rowCount() > 0) {
			$row = $saleDetailsStatement->fetch(PDO::FETCH_ASSOC);
			echo json_encode($row);
		}
		$saleDetailsStatement->closeCursor();
	}
?>