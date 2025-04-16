<?php
session_start();
require '../php/db.php'; // Include database connection

// Check if the user is logged in and is an organizer
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

// Initialize variables
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $status = 'pending';
    $organizer_id = $_SESSION['user_id']; // Get organizer ID from session

    // Handle file upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create uploads directory if it doesn't exist
        }
        $target_file = $target_dir . time() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file; // Save the file path
        } else {
            $message = "Error: Unable to upload image.";
        }
    }

    // Insert challenge into the database
    $sql = "INSERT INTO challenges (title, description, image, status, start_date, end_date, organizer_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $title, $description, $image, $status, $start_date, $end_date, $organizer_id);

    if ($stmt->execute()) {
        $message = "Challenge submitted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/dashboardstyles.css">
    <title>Submit Challenge</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
<header>
    <a href="dashboard.php" class="logo">
      <img src="../images/recipe.png" alt="Company Logo" class="logo-img">
    </a>
    <nav>
      <ul>
        <li><a href="submit-challenges.php">Submit Challenges</a></li>
        <li><a href="view-submissions.php">View Submissions</a></li>
        <li><a href="fetch-challenges.php">Event Management</a></li>
      </ul>
      <div class="search-bar">
        <input type="text" placeholder="Search in site">
      </div>
      <div class="user-dropdown">
        <img src="../images/user.png" alt="User Icon" class="user-icon" width="31" height="30">
        <div class="dropdown-content">
          <a href="../php/profile.php">My Account</a>
          <a href="tier.php">Tier</a>
          <a href="../php/logout.php">Log Out</a>
        </div>
      </div>
    </nav>
</header>
    <div class="container">
        <h1>Submit a New Challenge</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="submit-challenges.php" method="POST" enctype="multipart/form-data">
            <label for="title">Challenge Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter challenge title" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="5" placeholder="Enter challenge description" required></textarea>

            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>

            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>

            <label for="image">Upload Challenge Image:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Submit Challenge</button>
        </form>
    </div>
</body>
</html>
