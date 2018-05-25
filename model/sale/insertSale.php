<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['saleDetailsItemNumber'])){
		
		$itemNumber = htmlentities($_POST['saleDetailsItemNumber']);
		$itemName = htmlentities($_POST['saleDetailsItemName']);
		$discount = htmlentities($_POST['saleDetailsDiscount']);
		$quantity = htmlentities($_POST['saleDetailsQuantity']);
		$unitPrice = htmlentities($_POST['saleDetailsUnitPrice']);
		$customerID = htmlentities($_POST['saleDetailsCustomerID']);
		$customerName = htmlentities($_POST['saleDetailsCustomerName']);
		$saleDate = htmlentities($_POST['saleDetailsSaleDate']);
		
		// Check if mandatory fields are not empty
		if(!empty($itemNumber) && isset($customerID) && isset($saleDate) && isset($quantity) && isset($unitPrice)){
			
			// Sanitize item number
			$itemNumber = filter_var($itemNumber, FILTER_SANITIZE_STRING);
			
			// Validate item quantity. It has to be a number
			if(filter_var($quantity, FILTER_VALIDATE_INT) === 0 || filter_var($quantity, FILTER_VALIDATE_INT)){
				// Valid quantity
			} else {
				// Quantity is not a valid number
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid number for quantity</div>';
				exit();
			}
			
			// Check if customerID is empty
			if($customerID == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a Customer ID.</div>';
				exit();
			}
			
			// Validate customerID
			if(filter_var($customerID, FILTER_VALIDATE_INT) === 0 || filter_var($customerID, FILTER_VALIDATE_INT)){
				// Valid customerID
			} else {
				// customerID is not a valid number
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid Customer ID</div>';
				exit();
			}
			
			// Check if itemNumber is empty
			if($itemNumber == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Item Number.</div>';
				exit();
			}
			
			// Check if unit price is empty
			if($unitPrice == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Unit Price.</div>';
				exit();
			}
			
			// Validate unit price. It has to be a number or floating point value
			if(filter_var($unitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($unitPrice, FILTER_VALIDATE_FLOAT)){
				// Valid float (unit price)
			} else {
				// Unit price is not a valid number
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid number for unit price</div>';
				exit();
			}
			
			// Validate discount only if it's provided
			if(!empty($discount)){
				if(filter_var($discount, FILTER_VALIDATE_FLOAT) === false){
					// Discount is not a valid floating point number
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid discount amount</div>';
					exit();
				}
			}

			// Calculate the stock values
			$stockSql = 'SELECT stock FROM item WHERE itemNumber = :itemNumber';
			$stockStatement = $conn->prepare($stockSql);
			$stockStatement->execute(['itemNumber' => $itemNumber]);
			if($stockStatement->rowCount() > 0){
				// Item exits in DB, therefore, can proceed to a sale
				$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
				$currentQuantityInItemsTable = $row['stock'];
				
				if($currentQuantityInItemsTable <= 0) {
					// If currentQuantityInItemsTable is <= 0, stock is empty! that means we can't make a sell. Hence abort.
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Stock is empty. Therefore, can\'t make a sale. Please select a different item.</div>';
					exit();
				} elseif ($currentQuantityInItemsTable < $quantity) {
					// Requested sale quantity is higher than available item quantity. Hence abort 
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Not enough stock available for this sale. Therefore, can\'t make a sale. Please select a different item.</div>';
					exit();
				}
				else {
					// Has at least 1 or more in stock, hence proceed to next steps
					$newQuantity = $currentQuantityInItemsTable - $quantity;
					
					// Check if the customer is in DB
					$customerSql = 'SELECT * FROM customer WHERE customerID = :customerID';
					$customerStatement = $conn->prepare($customerSql);
					$customerStatement->execute(['customerID' => $customerID]);
					
					if($customerStatement->rowCount() > 0){
						// Customer exits. That means both customer, item, and stocks are available. Hence start INSERT and UPDATE
						$customerRow = $customerStatement->fetch(PDO::FETCH_ASSOC);
						$customerName = $customerRow['fullName'];
						
						// INSERT data to sale table
						$insertSaleSql = 'INSERT INTO sale(itemNumber, itemName, discount, quantity, unitPrice, customerID, customerName, saleDate) VALUES(:itemNumber, :itemName, :discount, :quantity, :unitPrice, :customerID, :customerName, :saleDate)';
						$insertSaleStatement = $conn->prepare($insertSaleSql);
						$insertSaleStatement->execute(['itemNumber' => $itemNumber, 'itemName' => $itemName, 'discount' => $discount, 'quantity' => $quantity, 'unitPrice' => $unitPrice, 'customerID' => $customerID, 'customerName' => $customerName, 'saleDate' => $saleDate]);
						
						// UPDATE the stock in item table
						$stockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
						$stockUpdateStatement = $conn->prepare($stockUpdateSql);
						$stockUpdateStatement->execute(['stock' => $newQuantity, 'itemNumber' => $itemNumber]);
						
						echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Sale details added to DB and stocks updated.</div>';
						exit();
						
					} else {
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Customer does not exist.</div>';
						exit();
					}
				}
				
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Item already exists in DB. Please click the <strong>Update</strong> button to update the details. Or use a different Item Number.</div>';
				exit();
			} else {
				// Item does not exist, therefore, you can't make a sale from it
				echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Item does not exist in DB.</div>';
				exit();
			}

		} else {
			// One or more mandatory fields are empty. Therefore, display a the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter all fields marked with a (*)</div>';
			exit();
		}
	}
?>