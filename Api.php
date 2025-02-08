<?php

use ScandiWeb\DataBase\Concrete\DBConnection;
use ScandiWeb\Queries\Contract\AbstractPRQ;
use ScandiWeb\Queries\Factory\QueriesFactory;

// CORS and method handling
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
    http_response_code(204);
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';

function handleRequest() {
    try {
        // Validate request method based on endpoint
        $method = $_SERVER['REQUEST_METHOD'];
        $data = json_decode(file_get_contents('php://input'), true);

        // Connection setup
        $dbConnection = new DBConnection();
        $connection = $dbConnection->connect();

        // Route request based on method and data
        switch ($method) {
            case 'POST':
                if (isset($data['type'])) {
                    // Insert request
                    $queries = QueriesFactory::createInstance($data['type']);
                    $result = $queries->InsertQuery($data,$connection);
                } else {
                    throw new Exception('Invalid request payload');
                }

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Data processed successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error processing data']);
                }
                break;
            case 'DELETE':
                if (isset($data['modelType'])) {
                    $queries = QueriesFactory::createInstance($data['modelType']);
                    $result = $queries->DeleteQuery($data, $connection);
                } else {
                    throw new Exception('Invalid request payload');
                }

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Data processed successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error processing data']);
                }
                break;
            case 'GET':
                // Select all products
                $data = AbstractPRQ::SelectProducts($connection);
                echo json_encode($data);
                break;

            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
                break;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Execute the request handler
handleRequest();