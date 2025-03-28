<?php
// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database and object files
include_once '../config/database.php';
include_once '../models/User.php';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate input
if(!empty($data->username) && !empty($data->password)) {
    // Create user object
    $user = new User($db);

    // Set property values
    $user->username = $data->username;
    $user->password = $data->password;

    // Attempt login
    $result = $user->login();

    if($result) {
        // Login successful
        http_response_code(200);
        echo json_encode(array(
            "message" => "Login successful.",
            "user" => array(
                "id" => $result['id'],
                "username" => $result['username']
            )
        ));
    } else {
        // Login failed
        http_response_code(401);
        echo json_encode(array("message" => "Invalid username or password."));
    }
} else {
    // Missing required data
    http_response_code(400);
    echo json_encode(array("message" => "Unable to login. Username and password are required."));
}
?> 