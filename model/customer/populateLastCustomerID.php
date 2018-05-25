<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$sql = "SELECT MAX(customerID) FROM customer";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	echo $row['MAX(customerID)'];
	$stmt->closeCursor();
?>