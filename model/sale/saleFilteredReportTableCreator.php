<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	$uPrice = 0;
	$qty = 0;
	$totalPrice = 0;
	
	if(isset($_POST['startDate'])){
		$startDate = htmlentities($_POST['startDate']);
		$endDate = htmlentities($_POST['endDate']);
		
		$saleFilteredReportSql = 'SELECT * FROM sale WHERE saleDate BETWEEN :startDate AND :endDate';
		$saleFilteredReportStatement = $conn->prepare($saleFilteredReportSql);
		$saleFilteredReportStatement->execute(['startDate' => $startDate, 'endDate' => $endDate]);

		$output = '<table id="saleFilteredReportsTable" class="table table-sm table-striped table-bordered table-hover" style="width:100%">
					<thead>
						<tr>
							<th>Sale ID</th>
							<th>Item Number</th>
							<th>Customer ID</th>
							<th>Customer Name</th>
							<th>Item Name</th>
							<th>Sale Date</th>
							<th>Discount %</th>
							<th>Quantity</th>
							<th>Unit Price</th>
							<th>Total Price</th>
						</tr>
					</thead>
					<tbody>';
		
		// Create table rows from the selected data
		while($row = $saleFilteredReportStatement->fetch(PDO::FETCH_ASSOC)){
			$uPrice = $row['unitPrice'];
			$qty = $row['quantity'];
			$discount = $row['discount'];
			$totalPrice = $uPrice * $qty * ((100 - $discount)/100);
		
			$output .= '<tr>' .
							'<td>' . $row['saleID'] . '</td>' .
							'<td>' . $row['itemNumber'] . '</td>' .
							'<td>' . $row['customerID'] . '</td>' .
							'<td>' . $row['customerName'] . '</td>' .
							'<td>' . $row['itemName'] . '</td>' .
							'<td>' . $row['saleDate'] . '</td>' .
							'<td>' . $row['discount'] . '</td>' .
							'<td>' . $row['quantity'] . '</td>' .
							'<td>' . $row['unitPrice'] . '</td>' .
							'<td>' . $totalPrice . '</td>' .
						'</tr>';
		}
		
		$saleFilteredReportStatement->closeCursor();
		
		$output .= '</tbody>
						<tfoot>
							<tr>
								<th>Total</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</tfoot>
					</table>';
		echo $output;
	}
?>


