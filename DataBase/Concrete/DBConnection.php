<?php
namespace ScandiWeb\DataBase\Concrete;

use Exception;
use mysqli;
use ScandiWeb\DataBase\Contract\IDBConnection;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

class DBConnection implements IDBConnection
{
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    private $connection;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        // Load the .env file
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        // Now get the environment variables
        $this->host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $this->username = $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
        $this->database = $_ENV['DB_DATABASE'] ?? 'Scandiweb';
        $this->port = $_ENV['DB_PORT'] ?? 3306;

        // Debugging: Check if variables are being loaded correctly
        if (!$this->host || !$this->username || !$this->database) {
            throw new Exception("Database connection parameters are not set correctly.");
        }
    }

    /**
     * @throws Exception
     */
    public function connect(): mysqli
    {
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database,
                $this->port
            );

            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }

            return $this->connection;
        } catch (Exception $e) {
            throw new Exception("Database connection error: " . $e->getMessage());
        }
    }

    public function close()
    {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}