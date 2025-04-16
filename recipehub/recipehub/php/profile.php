<?php
session_start(); // Start session handling

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

include 'db.php'; // Include the database connection file

$username = $_SESSION['username'];
$userData = [];

// Fetch user details from the database
try {
    $stmt = $conn->prepare("SELECT username, email, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }
} catch (Exception $e) {
    echo "Error fetching user details: " . $e->getMessage();
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account</title>
  <link rel="stylesheet" href="../css/dashboardstyles.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Playfair Display', serif;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 20px;
    }

    h1 {
      font-size: 28px;
      text-align: center;
      margin-bottom: 20px;
    }

    .profile-details {
      font-size: 18px;
      line-height: 1.6;
    }

    .profile-details strong {
      font-weight: bold;
    }

    .logout-btn {
      margin-top: 20px;
      display: block;
      width: 100%;
      text-align: center;
      background-color: #e74c3c;
      color: #ffffff;
      text-decoration: none;
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s ease;
    }

    .logout-btn:hover {
      background-color: #c0392b;
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
        <li><a href="../client/dashboard.php">Home</a></li>
        <li><a href="../client/addrecipe.php">Add Recipes</a></li>
        <li><a href="../client/recipecollection.php">Recipe Collection</a></li>
        <li><a href="../client/challenges.php">Community Challenges</a></li>
        <li><a href="../client/foodfacts.php">Food Facts</a></li>
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
          <a href="../client/tier.php">Tier</a>
          <a href="../php/logout.php">Log Out</a>
        </div>
    </nav>
  </header>
  <div class="container">
    <h1>My Account</h1>
    <div class="profile-details">
      <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
      <p><strong>Role:</strong> <?php echo htmlspecialchars($userData['role']); ?></p>
    </div>
    <a href="../php/logout.php" class="logout-btn">Log Out</a>
  </div>
</body>
</html>
