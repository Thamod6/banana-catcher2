<?php
require_once '../includes/session.php';
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit();
}

// Get POST data
$data = json_decode(file_get_contents("php://input"));

if (!empty($data->score)) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Prepare query
    $query = "INSERT INTO scores (user_id, score, level) VALUES (:user_id, :score, :level)";
    $stmt = $db->prepare($query);
    
    // Sanitize and bind values
    $user_id = $_SESSION['user_id'];
    $score = htmlspecialchars(strip_tags($data->score));
    $level = isset($data->level) ? htmlspecialchars(strip_tags($data->level)) : 1;
    
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":score", $score);
    $stmt->bindParam(":level", $level);
    
    // Execute query
    if($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array(
            "message" => "Score saved successfully",
            "score" => $score,
            "level" => $level
        ));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to save score"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Score is required"));
}
?> 