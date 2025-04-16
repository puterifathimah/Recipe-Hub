<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "recipe_hub";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipeName = $conn->real_escape_string($_POST['recipeName'] ?? '');
    $ingredients = $conn->real_escape_string($_POST['ingredients'] ?? '');
    $steps = $conn->real_escape_string($_POST['steps'] ?? '');
    $mediaPath = null;

    if (empty($recipeName) || empty($ingredients) || empty($steps)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm', 'video/ogg'];
    $maxFileSize = 200 * 1024 * 1024; // 200 MB

    if (isset($_FILES['photos']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['photos']['name']);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = time() . "_" . uniqid() . "." . $fileExtension;
        $targetFilePath = $targetDir . $newFileName;

        $fileType = mime_content_type($_FILES['photos']['tmp_name']);
        $fileSize = $_FILES['photos']['size'];

        if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true)) {
            echo json_encode(["status" => "error", "message" => "Failed to create upload directory."]);
            exit;
        }

        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(["status" => "error", "message" => "Invalid file type. Only images and videos are allowed."]);
            exit;
        }

        if (in_array($fileExtension, ['mp4', 'webm', 'ogg']) && $fileSize > $maxFileSize) {
            echo json_encode(["status" => "error", "message" => "Video file size exceeds 200 MB."]);
            exit;
        }

        if (!move_uploaded_file($_FILES['photos']['tmp_name'], $targetFilePath)) {
            echo json_encode(["status" => "error", "message" => "Failed to upload file."]);
            exit;
        }

        $mediaPath = $targetFilePath;
    }

    $sql = "INSERT INTO recipes (name, ingredients, steps, media) VALUES ('$recipeName', '$ingredients', '$steps', '$mediaPath')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Recipe submitted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save recipe: " . $conn->error]);
    }
}

$conn->close();
?>
