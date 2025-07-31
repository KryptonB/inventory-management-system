<?php
use PHPUnit\Framework\TestCase;
use Model\PurchaseModel;

require_once __DIR__ . '/../model/purchase/PurchaseModel.php';

class PurchaseTest extends TestCase {
    private $pdo;
    private $purchaseModel;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tabel item sesuai struktur database
        $this->pdo->exec("
            CREATE TABLE item (
                itemNumber INTEGER PRIMARY KEY,
                itemName TEXT,
                category TEXT,
                costPrice REAL,
                sellingPrice REAL,
                discount REAL,
                stock INTEGER,
                image TEXT,
                status TEXT
            );
        ");

        // Masukkan item dummy, itemNumber = 1
        $this->pdo->exec("
            INSERT INTO item (itemNumber, itemName, category, costPrice, sellingPrice, discount, stock, image, status)
            VALUES (1, 'Pensil', 'Alat Tulis', 1000, 1500, 0, 10, 'pensil.jpg', 'active');
        ");

        // Tabel vendor
        $this->pdo->exec("
            CREATE TABLE vendor (
                vendorID INTEGER PRIMARY KEY,
                fullName TEXT
            );
        ");

        // Masukkan vendor
        $this->pdo->exec("
            INSERT INTO vendor (vendorID, fullName)
            VALUES (1, 'CV Kertas Jaya');
        ");

        // Tabel purchase
        $this->pdo->exec("
            CREATE TABLE purchase (
                purchaseID INTEGER PRIMARY KEY AUTOINCREMENT,
                itemNumber INTEGER,
                itemName TEXT,
                unitPrice REAL,
                quantity INTEGER,
                vendorName TEXT,
                vendorID INTEGER,
                purchaseDate TEXT
            );
        ");

        $this->purchaseModel = new PurchaseModel($this->pdo);
    }


    public function testAddNewPurchase() {
        $data = [
            'itemNumber' => 1,
            'quantity' => 5,
            'unitPrice' => 1500,
            'itemName' => 'Pensil',
            'purchaseDate' => '2025-07-30',
            'vendorName' => 'CV Kertas Jaya'
        ];

        $result = $this->purchaseModel->addPurchase($data);
        $this->assertEquals('success', $result);
    }

}
