<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and object files
include_once '../config/database.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle different HTTP methods
switch($method) {
    case 'GET':
        // Handle GET request
        echo json_encode(array("message" => "GET request received"));
        break;
        
    case 'POST':
        // Handle POST request
        echo json_encode(array("message" => "POST request received"));
        break;
        
    case 'PUT':
        // Handle PUT request
        echo json_encode(array("message" => "PUT request received"));
        break;
        
    case 'DELETE':
        // Handle DELETE request
        echo json_encode(array("message" => "DELETE request received"));
        break;
        
    default:
        // Handle invalid request method
        http_response_code(405);
        echo json_encode(array("message" => "Method not allowed"));
        break;
}
?> 