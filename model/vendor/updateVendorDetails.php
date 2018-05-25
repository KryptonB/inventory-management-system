<?php
	require_once('../../inc/config/constants.php');
	require_once('../../inc/config/db.php');
	
	// Check if the POST query is received
	if(isset($_POST['vendorDetailsVendorID'])) {
		
		$vendorDetailsVendorID = htmlentities($_POST['vendorDetailsVendorID']);
		$vendorDetailsVendorFullName = htmlentities($_POST['vendorDetailsVendorFullName']);
		$vendorDetailsVendorMobile = htmlentities($_POST['vendorDetailsVendorMobile']);
		$vendorDetailsVendorPhone2 = htmlentities($_POST['vendorDetailsVendorPhone2']);
		$vendorDetailsVendorEmail = htmlentities($_POST['vendorDetailsVendorEmail']);
		$vendorDetailsVendorAddress = htmlentities($_POST['vendorDetailsVendorAddress']);
		$vendorDetailsVendorAddress2 = htmlentities($_POST['vendorDetailsVendorAddress2']);
		$vendorDetailsVendorCity = htmlentities($_POST['vendorDetailsVendorCity']);
		$vendorDetailsVendorDistrict = htmlentities($_POST['vendorDetailsVendorDistrict']);
		$vendorDetailsStatus = htmlentities($_POST['vendorDetailsStatus']);
		
		
		// Check if vendorID is given or not. If not given, the display a message
		if(!empty($vendorDetailsVendorID)){
			// Check if mandatory fields are not empty
			if(!empty($vendorDetailsVendorFullName) && !empty($vendorDetailsVendorMobile) && !empty($vendorDetailsVendorAddress)) {
				
				// Validate mobile number
				if(filter_var($vendorDetailsVendorMobile, FILTER_VALIDATE_INT) === 0 || filter_var($vendorDetailsVendorMobile, FILTER_VALIDATE_INT)) {
					// Mobile number is valid
				} else {
					// Mobile number is not valid
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid mobile number</div>';
					exit();
				}
				
				// Check if vendorID field is empty. If so, display an error message
				// We have to specifically tell this to user because the (*) mark is not added to that field
				if(empty($vendorDetailsVendorID)){
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter the Vendor ID to update that vendor. You can find the Vendor ID using the Search tab</div>';
					exit();
				}
				
				// Validate second phone number only if it's provided by user
				if(isset($vendorDetailsVendorPhone2)){
					if(filter_var($vendorDetailsVendorPhone2, FILTER_VALIDATE_INT) === 0 || filter_var($vendorDetailsVendorPhone2, FILTER_VALIDATE_INT)) {
						// Phone number 2 is valid
					} else {
						// Phone number 2 is not valid
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid number for phone number 2.</div>';
						exit();
					}
				}
				
				// Validate email only if it's provided by user
				if(!empty($vendorDetailsVendorEmail)) {
					if (filter_var($vendorDetailsVendorEmail, FILTER_VALIDATE_EMAIL) === false) {
						// Email is not valid
						echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter a valid email</div>';
						exit();
					}
				}

				// Check if the given vendorID is in the DB
				$vendorIDSelectSql = 'SELECT vendorID FROM vendor WHERE vendorID = :vendorID';
				$vendorIDSelectStatement = $conn->prepare($vendorIDSelectSql);
				$vendorIDSelectStatement->execute(['vendorID' => $vendorDetailsVendorID]);
				
				if($vendorIDSelectStatement->rowCount() > 0) {
					
					// vendorID is available in DB. Therefore, we can go ahead and UPDATE its details
					
					// But first update the purchase details vendor name in the purchase table
					$purchaseVendorNameSql = 'UPDATE purchase SET vendorName = :vendorName WHERE vendorID = :vendorID';
					$purchaseVendorNameStatement = $conn->prepare($purchaseVendorNameSql);
					$purchaseVendorNameStatement->execute(['vendorName' => $vendorDetailsVendorFullName, 'vendorID' => $vendorDetailsVendorID]);
					
					// Construct the UPDATE query
					$updateVendorDetailsSql = 'UPDATE vendor SET fullName = :fullName, email = :email, mobile = :mobile, phone2 = :phone2, address = :address, address2 = :address2, city = :city, district = :district, status = :status WHERE vendorID = :vendorID';
					$updateVendorDetailsStatement = $conn->prepare($updateVendorDetailsSql);
					$updateVendorDetailsStatement->execute(['fullName' => $vendorDetailsVendorFullName, 'email' => $vendorDetailsVendorEmail, 'mobile' => $vendorDetailsVendorMobile, 'phone2' => $vendorDetailsVendorPhone2, 'address' => $vendorDetailsVendorAddress, 'address2' => $vendorDetailsVendorAddress2, 'city' => $vendorDetailsVendorCity, 'district' => $vendorDetailsVendorDistrict, 'vendorID' => $vendorDetailsVendorID, 'status' => $vendorDetailsStatus]);
					
					// UPDATE vendor name in purchase table too
					$updateVendorInPurchaseTableSql = 'UPDATE purchase SET vendorName = :vendorName WHERE vendorID = :vendorID';
					$updateVendorInPurchaseTableStatement = $conn->prepare($updateVendorInPurchaseTableSql);
					$updateVendorInPurchaseTableStatement->execute(['vendorName' => $vendorDetailsVendorFullName, 'vendorID' => $vendorDetailsVendorID]);
					
					echo '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Vendor details updated.</div>';
					exit();
				} else {
					// vendorID is not in DB. Therefore, stop the update and quit
					echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Vendor ID does not exist in DB. Therefore, update not possible.</div>';
					exit();
				}
				
			} else {
				// One or more mandatory fields are empty. Therefore, display the error message
				echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter all fields marked with a (*)</div>';
				exit();
			}
		} else {
			// vendorID is not given by user. Hence, can't update
			echo '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>Please enter the Vendor ID to update that vendor. You can find the Vendor ID using the Search tab</div>';
			exit();
		}
	}
?>