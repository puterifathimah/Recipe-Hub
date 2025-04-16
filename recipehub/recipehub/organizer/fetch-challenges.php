<?php
require 'db_connection.php'; // Include database connection

// Check database connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

try {
    // Query to fetch approved and ongoing challenges
    $sql = "SELECT id, title, description, image, start_date, end_date 
            FROM challenges 
            WHERE status = 'approved' AND end_date >= CURDATE()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare challenges data
    $challenges = [];
    while ($row = $result->fetch_assoc()) {
        $challenges[] = $row;
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Challenges fetched successfully',
        'data' => $challenges
    ]);
} catch (Exception $e) {
    // Handle query error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch challenges',
        'error' => $e->getMessage()
    ]);
} finally {
    // Close connection
    $conn->close();
}
