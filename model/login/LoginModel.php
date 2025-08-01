<?php
namespace Model;

use PDO;

class LoginModel {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function validateUser($username, $password) {
        echo "\n==[ validateUser Debug ]==\n";
        echo "Input username: $username\n";
        echo "Input password (plain): $password\n";
        $password = md5($password);
        echo "Hashed password: $password\n";

        $sql = 'SELECT * FROM user WHERE username = :username AND password = :password';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'password' => $password
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Result from DB: "; var_dump($result);

        return $result ?: false;
    }

}
