<?php
include '../php/db.php'; // Include database connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $recipeId = intval($data['recipe_id']);

    // Increment the likes for the specified recipe
    $stmt = $conn->prepare("UPDATE recipes SET likes = likes + 1 WHERE id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $stmt->close();

    // Get the updated likes count
    $stmt = $conn->prepare("SELECT likes FROM recipes WHERE id = ?");
    $stmt->bind_param("i", $recipeId);
    $stmt->execute();
    $stmt->bind_result($likes);
    $stmt->fetch();
    $stmt->close();

    // Return the updated likes count
    echo json_encode(['likes' => $likes]);
}

$conn->close();
?>
