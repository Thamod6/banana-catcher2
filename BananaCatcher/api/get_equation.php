<?php
require_once '../includes/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit();
}

// Function to fetch equation from external API
function getEquationFromAPI() {
    $api_url = 'http://marcconrad.com/uob/banana/api.php?out=json';
    
    // Fetch data from API
    $response = file_get_contents($api_url);
    
    if ($response === false) {
        throw new Exception('Failed to fetch equation from API');
    }
    
    // Parse the response
    $data = json_decode($response, true);
    
    if (!$data) {
        throw new Exception('Invalid response from API');
    }
    
    // Return the data directly
    return $data;
}

try {
    // Get equation from API
    $result = getEquationFromAPI();
    
    // Send response
    header('Content-Type: application/json');
    echo json_encode($result);
} catch (Exception $e) {
    // Log the error for debugging
    error_log("Error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode(array("error" => "Failed to fetch equation"));
}
?> 