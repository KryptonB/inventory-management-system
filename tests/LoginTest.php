<?php
use PHPUnit\Framework\TestCase;
use Model\LoginModel;

require_once __DIR__ . '/../model/login/LoginModel.php';

class LoginTest extends TestCase {
    private $pdo;

    protected function setUp(): void {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Buat tabel user lengkap dengan kolom status
        $this->pdo->exec("
            CREATE TABLE user (
                userID INTEGER PRIMARY KEY AUTOINCREMENT,
                fullName TEXT,
                username TEXT,
                password TEXT,
                status TEXT DEFAULT 'Active'
            );
        ");

        // Masukkan user dummy lengkap
        $stmt = $this->pdo->prepare("INSERT INTO user (fullName, username, password, status) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin', md5('password123'), 'Active']);

        // Debug isi tabel user
        $stmt = $this->pdo->query("SELECT * FROM user");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function testValidLogin() {
        $login = new LoginModel($this->pdo);
        $result = $login->validateUser('admin', 'password123');
        var_dump($result); // opsional untuk debug
        $this->assertIsArray($result);
    }

    public function testInvalidLogin() {
        $login = new LoginModel($this->pdo);
        $result = $login->validateUser('admin', 'wrongpass');
        $this->assertFalse($result);
    }
}
