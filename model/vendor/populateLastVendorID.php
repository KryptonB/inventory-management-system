<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$sql = "SELECT MAX(vendorID) FROM vendor";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	echo $row['MAX(vendorID)'];
	$stmt->closeCursor();
?>