<?php

class Database {

    // Database connection details (filled in by the constructor)
    private string $host;
    private string $dbName;
    private string $username;
    private string $password;

    public function __construct()
    {
        if ($this->isLocal()) {

            // LOCAL (XAMPP / Laragon)
            $this->host     = 'localhost';
            $this->dbName   = 'psp_student_portal';
            $this->username = 'root';
            $this->password = '';

        } else {

            // LIVE SERVER
            // Credentials are kept in database.live.php, which is NOT pushed
            // to GitHub (see .gitignore). Upload it to the server manually.
            $liveFile = __DIR__ . '/database.live.php';

            if (!file_exists($liveFile)) {
                http_response_code(500);
                exit('Live database configuration is missing.');
            }

            $live = require $liveFile;

            $this->host     = $live['host'];
            $this->dbName   = $live['dbname'];
            $this->username = $live['username'];
            $this->password = $live['password'];
        }
    }

    // Check whether the app is running on the local machine
    private function isLocal(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $host = strtolower(explode(':', $host)[0]);

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }

    // Connect to the MySQL database
    public function connect(): PDO {
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";

        try {
            return new PDO($dsn, $this->username, $this->password, [
                // Show database errors
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Return database results as associative arrays
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // Use real prepared statements for security
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            // Don't show database details to visitors on the live site
            error_log('Database connection failed: ' . $e->getMessage());

            if ($this->isLocal()) {
                exit('Database connection failed: ' . $e->getMessage());
            }

            http_response_code(500);
            exit('Database connection failed. Please try again later.');
        }
    }
}
