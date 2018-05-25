<?php
	$itemDetailsSql = 'SELECT itemName FROM item';
	$itemDetailsStatement = $conn->prepare($itemDetailsSql);
	$itemDetailsStatement->execute();
	
	if($itemDetailsStatement->rowCount() > 0) {
		while($row = $itemDetailsStatement->fetch(PDO::FETCH_ASSOC)) {
			echo '<option>' . $row['itemName'] . '</option>';
		}
	}
	$itemDetailsStatement->closeCursor();
?>