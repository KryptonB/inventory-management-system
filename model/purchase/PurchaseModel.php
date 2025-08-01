<?php
namespace Model;

use PDO;

class PurchaseModel {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function addPurchase($data) {
        $itemNumber = $data['itemNumber'];
        $quantity = $data['quantity'];
        $unitPrice = $data['unitPrice'];
        $itemName = $data['itemName'];
        $purchaseDate = $data['purchaseDate'];
        $vendorName = $data['vendorName'];

        $stockStmt = $this->conn->prepare('SELECT stock FROM item WHERE itemNumber = :itemNumber');
        $stockStmt->execute(['itemNumber' => $itemNumber]);

        $stockRow = $stockStmt->fetch(PDO::FETCH_ASSOC);
        if (!$stockRow) {
            return 'Item not found';
        }


        $vendorStmt = $this->conn->prepare('SELECT * FROM vendor WHERE fullName = :fullName');
        $vendorStmt->execute(['fullName' => $vendorName]);
        $vendor = $vendorStmt->fetch(PDO::FETCH_ASSOC);

        $vendorID = $vendor['vendorID'] ?? null;

        $insertStmt = $this->conn->prepare('
            INSERT INTO purchase (itemNumber, purchaseDate, itemName, unitPrice, quantity, vendorName, vendorID)
            VALUES (:itemNumber, :purchaseDate, :itemName, :unitPrice, :quantity, :vendorName, :vendorID)
        ');
        $insertStmt->execute([
            'itemNumber' => $itemNumber,
            'purchaseDate' => $purchaseDate,
            'itemName' => $itemName,
            'unitPrice' => $unitPrice,
            'quantity' => $quantity,
            'vendorName' => $vendorName,
            'vendorID' => $vendorID
        ]);

        $stockRow = $stockStmt->fetch(PDO::FETCH_ASSOC);
        $newStock = $stockRow['stock'] + $quantity;

        $updateStockStmt = $this->conn->prepare('UPDATE item SET stock = :stock WHERE itemNumber = :itemNumber');
        $updateStockStmt->execute(['stock' => $newStock, 'itemNumber' => $itemNumber]);

        return 'success';
    }
}
