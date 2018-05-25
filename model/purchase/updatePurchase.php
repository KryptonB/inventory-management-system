<?php

// Updated script - 2018-05-09

	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	if(isset($_POST['purchaseDetailsPurchaseID'])){

		$purchaseDetailsItemNumber = htmlentities($_POST['purchaseDetailsItemNumber']);
		$purchaseDetailsPurchaseDate = htmlentities($_POST['purchaseDetailsPurchaseDate']);
		$purchaseDetailsItemName = htmlentities($_POST['purchaseDetailsItemName']);
		$purchaseDetailsQuantity = htmlentities($_POST['purchaseDetailsQuantity']);
		$purchaseDetailsUnitPrice = htmlentities($_POST['purchaseDetailsUnitPrice']);
		$purchaseDetailsPurchaseID = htmlentities($_POST['purchaseDetailsPurchaseID']);
		$purchaseDetailsVendorName = htmlentities($_POST['purchaseDetailsVendorName']);
		
		$quantityInOriginalOrder = 0;
		$quantityInNewOrder = 0;
		$originalStockInItemTable = 0;
		$newStock = 0;
		$originalPurchaseItemNumber = '';
		
		// Check if mandatory fields are not empty
		if(isset($purchaseDetailsItemNumber) && isset($purchaseDetailsPurchaseDate) && isset($purchaseDetailsQuantity) && isset($purchaseDetailsUnitPrice)){
			
			// Sanitize item number
			$purchaseDetailsItemNumber = filter_var($purchaseDetailsItemNumber, FILTER_SANITIZE_STRING);
			
			// Validate item quantity. It has to be an integer
			if(filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT) === 0 || filter_var($purchaseDetailsQuantity, FILTER_VALIDATE_INT)){
				// Quantity is valid
			} else {
				// Quantity is not a valid number
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid number for quantity.</div>';
				exit();
			}
			
			// Validate unit price. It has to be an integer or floating point value
			if(filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT) === 0.0 || filter_var($purchaseDetailsUnitPrice, FILTER_VALIDATE_FLOAT)){
				// Valid unit price
			} else {
				// Unit price is not a valid number
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid number for unit price.</div>';
				exit();
			}
			
			// Check if purchaseID is empty
			if($purchaseDetailsPurchaseID == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a Purchase ID.</div>';
				exit();
			}
			
			// Check if itemNumber is empty
			if($purchaseDetailsItemNumber == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter Item Number.</div>';
				exit();
			}
			
			// Check if quantity is empty
			if($purchaseDetailsQuantity == ''){ 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter quantity.</div>';
				exit();
			}
			
			// Get the quantity and itemNumber in original purchase order
			$orginalPurchaseQuantitySql = 'SELECT * FROM purchase WHERE purchaseID = :purchaseID';
			$originalPurchaseQuantityStatement = $conn->prepare($orginalPurchaseQuantitySql);
			$originalPurchaseQuantityStatement->execute(['purchaseID' => $purchaseDetailsPurchaseID]);
			
			// Get the vendorId for the given vendorName
			$vendorIDsql = 'SELECT * FROM vendor WHERE fullName = :fullName';
			$vendorIDStatement = $conn->prepare($vendorIDsql);
			$vendorIDStatement->execute(['fullName' => $purchaseDetailsVendorName]);
			$row = $vendorIDStatement->fetch(PDO::FETCH_ASSOC);
			$vendorID = $row['vendorID'];
			
			if($originalPurchaseQuantityStatement->rowCount() > 0){
				
				// Purchase details exist in DB. Hence proceed to calculate the stock
				$originalQtyRow = $originalPurchaseQuantityStatement->fetch(PDO::FETCH_ASSOC);
				$quantityInOriginalOrder = $originalQtyRow['quantity'];
				$originalOrderItemNumber = $originalQtyRow['itemNumber'];

				// Check if the user wants to update the itemNumber too. In that case,
				// we need to remove the quantity of the original order for that item and 
				// update the new item details in the item table.
				// Check if the original itemNumber is the same as the new itemNumber
				if($originalOrderItemNumber !== $purchaseDetailsItemNumber) {
					// Item numbers are different. That means the user wants to update a new item number too
					// in that case, need to update both items' stocks.
						
					// Get the stock of the new item from item table
					$newItemCurrentStockSql = 'SELECT * FROM item WHERE itemNumber = :itemNumber';
					$newItemCurrentStockStatement = $conn->prepare($newItemCurrentStockSql);
					$newItemCurrentStockStatement->execute(['itemNumber' => $purchaseDetailsItemNumber]);
					
					if($newItemCurrentStockStatement->rowCount() < 1){
						// Item number is not in DB. Hence abort.
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Item Number does not exist in DB. If you want to update this item, please add it to DB first.</div>';
						exit();
					}
					
					// Calculate the new stock value for new item using the existing stock in item table
					$newItemRow = $newItemCurrentStockStatement->fetch(PDO::FETCH_ASSOC);
					$originalQuantityForNewItem = $newItemRow['stock'];
					$enteredQuantityForNewItem = $purchaseDetailsQuantity;
					$newItemNewStock = $originalQuantityForNewItem + $enteredQuantityForNewItem;
					
					// UPDATE the stock for new item in item table
					$newItemStockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
					$newItemStockUpdateStatement = $conn->prepare($newItemStockUpdateSql);
					$newItemStockUpdateStatement->execute(['stock' => $newItemNewStock, 'itemNumber' => $purchaseDetailsItemNumber]);
					
					// Get the current stock of the previous item
					$previousItemCurrentStockSql = 'SELECT * FROM item WHERE itemNumber=:itemNumber';
					$previousItemCurrentStockStatement = $conn->prepare($previousItemCurrentStockSql);
					$previousItemCurrentStockStatement->execute(['itemNumber' => $originalOrderItemNumber]);
					
					// Calculate the new stock value for the previous item using the existing stock in item table
					$previousItemRow = $previousItemCurrentStockStatement->fetch(PDO::FETCH_ASSOC);
					$currentQuantityForPreviousItem = $previousItemRow['stock'];
					$previousItemNewStock = $currentQuantityForPreviousItem - $quantityInOriginalOrder;
					
					// UPDATE the stock for previous item in item table
					$previousItemStockUpdateSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
					$previousItemStockUpdateStatement = $conn->prepare($previousItemStockUpdateSql);
					$previousItemStockUpdateStatement->execute(['stock' => $previousItemNewStock, 'itemNumber' => $originalOrderItemNumber]);
					
					// Finally UPDATE the purchase table for new item
					$updatePurchaseDetailsSql = 'UPDATE purchase SET itemNumber = :itemNumber, purchaseDate = :purchaseDate, itemName = :itemName, unitPrice = :unitPrice, quantity = :quantity, vendorName = :vendorName, vendorID = :vendorID WHERE purchaseID = :purchaseID';
					$updatePurchaseDetailsStatement = $conn->prepare($updatePurchaseDetailsSql);
					$updatePurchaseDetailsStatement->execute(['itemNumber' => $purchaseDetailsItemNumber, 'purchaseDate' => $purchaseDetailsPurchaseDate, 'itemName' => $purchaseDetailsItemName, 'unitPrice' => $purchaseDetailsUnitPrice, 'quantity' => $purchaseDetailsQuantity, 'vendorName' => $purchaseDetailsVendorName, 'vendorID' => $vendorID, 'purchaseID' => $purchaseDetailsPurchaseID]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Purchase details added to database and stock values updated.</div>';
					exit();
					
				} else {
					// Item numbers are equal. That means item number is valid
					
					// Get the quantity (stock) in item table
					$stockSql = 'SELECT * FROM item WHERE itemNumber=:itemNumber';
					$stockStatement = $conn->prepare($stockSql);
					$stockStatement->execute(['itemNumber' => $purchaseDetailsItemNumber]);
					
					if($stockStatement->rowCount() > 0){
						// Item exists in the item table, therefore, start inserting data to purchase table
						
						// Calculate the new stock value using the existing stock in item table
						$row = $stockStatement->fetch(PDO::FETCH_ASSOC);
						$quantityInNewOrder = $purchaseDetailsQuantity;
						$originalStockInItemTable = $row['stock'];
						$newStock = $originalStockInItemTable + ($quantityInNewOrder - $quantityInOriginalOrder);
						
						// Update the new stock value in item table.
						$updateStockSql = 'UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber';
						$updateStockStatement = $conn->prepare($updateStockSql);
						$updateStockStatement->execute(['stock' => $newStock, 'itemNumber' => $purchaseDetailsItemNumber]);
						
						// Next, update the purchase table
						$updatePurchaseDetailsSql = 'UPDATE purchase SET purchaseDate = :purchaseDate, unitPrice = :unitPrice, quantity = :quantity, vendorName = :vendorName, vendorID = :vendorID WHERE purchaseID = :purchaseID';
						$updatePurchaseDetailsStatement = $conn->prepare($updatePurchaseDetailsSql);
						$updatePurchaseDetailsStatement->execute(['purchaseDate' => $purchaseDetailsPurchaseDate, 'unitPrice' => $purchaseDetailsUnitPrice, 'quantity' => $purchaseDetailsQuantity, 'vendorName' => $purchaseDetailsVendorName, 'vendorID' => $vendorID, 'purchaseID' => $purchaseDetailsPurchaseID]);
						
						echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Purchase details added to database and stock values updated.</div>';
						exit();
						
					} else {
						// Item does not exist in item table, therefore, you can't update 
						// purchase details for it 
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Item does not exist in DB. Therefore, first enter this item to DB using the <strong>Item</strong> tab.</div>';
						exit();
					}	
					
				}
	
			} else {
				
				// PurchaseID does not exist in purchase table, therefore, you can't update it 
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Purchase details does not exist in DB for the given Purchase ID. Therefore, can\'t update.</div>';
				exit();
				
			}

		} else {
			// One or more mandatory fields are empty. Therefore, display the error message
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter all fields marked with a (*)</div>';
			exit();
		}
	}
?>