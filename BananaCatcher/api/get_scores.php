<?php
require_once '../includes/session.php';
require_once '../config/database.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(array("message" => "Unauthorized"));
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Query to get highest score for each player
    $query = "SELECT s.user_id, MAX(s.score) as score, u.username 
              FROM scores s 
              JOIN users u ON s.user_id = u.id 
              GROUP BY s.user_id, u.username 
              ORDER BY score DESC 
              LIMIT 100";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $scores = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $scores[] = array(
            "user_id" => $row['user_id'],
            "username" => $row['username'],
            "score" => $row['score']
        );
    }
    
    // Send response
    header('Content-Type: application/json');
    echo json_encode($scores);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Error fetching scores: " . $e->getMessage()));
}
?> 