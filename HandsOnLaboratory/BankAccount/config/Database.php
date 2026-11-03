<?php
// Make MySQL errors throw exceptions so try/catch in the pages works
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class Database {
    private $host     = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname   = 'bankaccount';

    protected $conn = null;

    // Opens the connection once and reuses it
    public function connect() {
        if ($this->conn === null) {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
            $this->conn->set_charset('utf8mb4');
        }
        return $this->conn;
    }
}
