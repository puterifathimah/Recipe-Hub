<?php
session_start();
require '../php/db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$username = $_SESSION['username'];
$sql = "SELECT points FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found!";
    exit();
}

$points = $user['points'];

// Determine the user's tier
$tier = "Bronze";
$nextTier = "Silver";
$nextTierPoints = 50;
if ($points >= 50 && $points < 200) {
    $tier = "Silver";
    $nextTier = "Gold";
    $nextTierPoints = 200;
} elseif ($points >= 200) {
    $tier = "Gold";
    $nextTier = null; // No next tier
}

// Calculate progress percentage
$progress = $nextTier ? ($points / $nextTierPoints) * 100 : 100;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Tier</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <style>

    /* CSS Styling */
    body {
      font-family: 'Playfair Display', serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      color: #333;
    }
    header, nav ul li a, .hero h1, .hero p, footer {
      font-family: 'Playfair Display', serif;
    }
    
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #f9f9f9;
      padding: 10px 5px;
      height: 50px; /* Adjust the height as needed */
      border-bottom: 1px solid #ddd;
    }
    
    .logo h1 {
      font-size: 24px;
      color: #2c3e50;
      margin: 0;
    }
    
    .logo p {
      margin: 0;
      font-size: 12px;
      color: #7f8c8d;
    }
    
    .logo-img {
      width: 90px; /* Adjust to your desired width */
      height: auto; /* Maintain aspect ratio */
    }

      /* Navigation bar styling */
    nav {
      display: flex;
      flex: 1; /* Allow nav to take remaining space */
      justify-content: space-between;
      align-items: center;
    }

    nav ul {
        list-style: none;
        display: flex;
        margin: 0 auto; /* Center the links */
        padding: 0;
        justify-content: center;
    }
    
    nav ul li {
        margin: 0 15px;
    }

    nav ul li a {
      text-decoration: none;
      color: #34495e;
      font-weight: bold;
    }
    
    nav ul li a:hover {
      color: #e67e22;
    }
    
    .search-bar {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-left: auto; /* Push the search bar and icon to the far right */
    }
    
    /* Align search input and user icon to the right */
    nav input[type="text"] {
      padding: 5px;
      border: 1px solid #ddd;
      border-radius: 4px;
      background-color: #f4caa6;
      color: #333;
      margin-right: 10px; /* Space between search and user icon */
      width: 200px; /* Set a fixed width for the search input */
}
/* User dropdown styling */
.user-dropdown {
      position: relative;
      display: inline-block;
    }
    
    .user-icon {
      width: 30px;
      height: 30px;
      cursor: pointer;
      border-radius: 50%; /* Circular icon */
      display: block;
    }
    
    /* Dropdown content */
    .dropdown-content {
      display: none; /* Hidden initially */
      position: absolute;
      top: 100%; /* Position below the icon */
      right: 0; /* Align to the right edge */
      background-color: white;
      min-width: 150px; /* Dropdown width */
      box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2); /* Adds shadow */
      z-index: 1000; /* Ensure it's on top */
      border-radius: 5px;
      overflow: hidden;
      transition: opacity 0.3s ease; /* Smooth transition for visibility */
      opacity: 0; /* Initially invisible */
      visibility: hidden; /* Prevent interaction */
    }
    
    .dropdown-content a {
      color: black;
      text-decoration: none;
      padding: 10px 15px;
      display: block;
      font-size: 14px;
    }
    
    .dropdown-content a:hover {
      background-color: #ffbc6a;
    }
    
    /* Show dropdown on hover */
    .user-dropdown:hover .dropdown-content {
      display: block; /* Make dropdown visible */
      opacity: 1; /* Fully visible */
      visibility: visible; /* Allow interaction */
    }

    main {
      padding: 10px;
    }

    h1 {
      font-size: 36px;
    }
    form {
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    form div {
      margin-bottom: 15px;
    }
    label {
      font-size: 18px;
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
    }
    input[type="text"], textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    textarea {
      height: 100px;
      resize: vertical;
    }
    input[type="file"] {
      margin-top: 10px;
    }
    button {
      background-color: #e67e22;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #d35400;
    }
    .success-message {
      display: none;
      margin-top: 20px;
      padding: 10px;
      background-color: #dff0d8;
      color: #3c763d;
      border: 1px solid #d6e9c6;
      border-radius: 5px;
    }

    footer {
      text-align: center;
      background-color: #f9f9f9;
      padding: 10px 0;
      border-top: 1px solid #ddd;
      margin-top: 20px;
    }

    .scroll-top {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: transparent; /* Remove background color */
      border: none; /* Remove border */
      padding: 0; /* Remove padding */
      cursor: pointer; /* Change cursor to pointer for better UX */
      width: 50px; /* Adjust size as needed */
      height: 50px; /* Adjust size as needed */
    }

    .scroll-top img {
      width: 80%; /* Make the image fill the button */
      height: 80%; /* Maintain aspect ratio */
      display: block; /* Prevent inline image spacing issues */
      border-radius: 30%; /* Optional: make the button circular */
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Optional: add a shadow for better visibility */
    }

    .scroll-top img:hover {
      transform: scale(1.1); /* Optional: Add a hover effect */
      transition: transform 0.3s ease;
    }
    body {
      font-family: 'Playfair Display', serif;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background-color: #f9f9f9;
      min-height: 100vh;
    }

    .container {
      background: white;
      width: 90%;
      max-width: 600px;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    h1 {
      font-size: 28px;
      margin-bottom: 10px;
    }

    .tier {
      font-size: 24px;
      font-weight: bold;
      color: #e67e22;
    }

    .points {
      margin: 10px 0;
      font-size: 18px;
    }

    .progress-bar {
      position: relative;
      background: #ddd;
      border-radius: 10px;
      height: 20px;
      width: 100%;
      margin: 20px 0;
    }

    .progress-bar-inner {
      background: #27ae60;
      height: 100%;
      border-radius: 10px;
      width: <?php echo $progress; ?>%;
      max-width: 100%;
      transition: width 0.5s ease-in-out;
    }

    .progress-percentage {
      position: absolute;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      font-size: 12px;
      color: white;
    }

    .tier-info {
      margin-top: 20px;
      font-size: 16px;
      color: #555;
    }

    .next-tier {
      font-size: 18px;
      margin-top: 10px;
      color: #2980b9;
    }

    .motivational {
      margin-top: 20px;
      font-style: italic;
      color: #34495e;
    }

    .badge {
      font-size: 50px;
      margin-top: 20px;
    }

    footer {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #888;
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
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="addrecipe.php">Add Recipes</a></li>
        <li><a href="recipecollection.php">Recipe Collection</a></li>
        <li><a href="challenge.html">Community Challenges</a></li>
        <li><a href="foodfacts.php">Food Facts</a></li>
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

  <main>
  <div class="container">
    <h1>Your Tier</h1>
    <div class="badge">
      <?php echo $tier === "Bronze" ? "🥉" : ($tier === "Silver" ? "🥈" : "🥇"); ?>
    </div>
    <p class="tier"><?php echo $tier; ?> Tier</p>
    <p class="points">You have <strong><?php echo $points; ?> points</strong>.</p>

    <?php if ($nextTier): ?>
      <div class="progress-bar">
        <div class="progress-bar-inner">
          <span class="progress-percentage"><?php echo floor($progress); ?>%</span>
        </div>
      </div>
      <p class="next-tier">Next Tier: <strong><?php echo $nextTier; ?></strong> (<?php echo $nextTierPoints; ?> points)</p>
    <?php else: ?>
      <p class="next-tier">Congratulations! You've reached the highest tier!</p>
    <?php endif; ?>

    <p class="motivational">
      <?php if ($tier === "Bronze"): ?>
        Keep going! You're on your way to Silver Tier. 🎉
      <?php elseif ($tier === "Silver"): ?>
        Great work! Gold Tier is within your reach! 🌟
      <?php else: ?>
        You're a Gold Tier champion! Keep being awesome! 🏆
      <?php endif; ?>
    </p>
  </div>
  </main>
  <footer>© 2022 RecipeHub. All Rights Reserved.</footer>
</body>
</html>
