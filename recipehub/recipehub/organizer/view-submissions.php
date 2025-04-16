<?php
session_start();
require '../php/db.php'; // Include the database connection

// Check if the user is logged in and is an organizer
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../index.php");
    exit();
}

// Fetch challenge_id from query string
$challenge_id = $_GET['challenge_id'] ?? null;

// Validate challenge_id
if (!$challenge_id || !is_numeric($challenge_id)) {
    die("Invalid Challenge ID.");
}

// Fetch submissions for the specified challenge
$sql = "SELECT id, name, email, recipe, message FROM registrations WHERE challenge_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $challenge_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare submissions data
$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submissions[] = $row;
}

// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/dashboardstyles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Submissions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: #34495e;
            color: white;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #555;
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
        <div class="dropdown-content">
          <a href="login.html">More</a>
        </div>
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
    </nav>
  </header>
    <h1>Submissions for Challenge ID: <?php echo htmlspecialchars($challenge_id); ?></h1>

    <?php if (count($submissions) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Recipe</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($submission['id']); ?></td>
                        <td><?php echo htmlspecialchars($submission['name']); ?></td>
                        <td><?php echo htmlspecialchars($submission['email']); ?></td>
                        <td>
                            <?php if ($submission['recipe']): ?>
                                <a href="uploads/<?php echo htmlspecialchars($submission['recipe']); ?>" target="_blank">View Recipe</a>
                            <?php else: ?>
                                No Recipe
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($submission['message']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No submissions found for this challenge.</p>
    <?php endif; ?>
</body>
</html>
