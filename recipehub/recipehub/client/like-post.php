<?php
include '../php/db.php'; // Include the database connection

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = intval($data['post_id']);

    // Increment likes in the database
    $stmt = $conn->prepare("UPDATE posts SET likes = likes + 1 WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->close();

    // Fetch the updated likes count
    $stmt = $conn->prepare("SELECT likes FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->bind_result($likes);
    $stmt->fetch();
    $stmt->close();

    // Return the updated likes count
    echo json_encode(['likes' => $likes]);
}
$conn->close();
?>
