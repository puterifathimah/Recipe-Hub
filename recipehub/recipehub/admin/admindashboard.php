<?php
session_start(); // Start session handling

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

include '../php/db.php'; // Include the database connection file

$adminName = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <style>
    /* General Styling */
    body {
      font-family: 'Playfair Display', serif;
      margin: 0;
      display: flex;
    }

    /* Sidebar Styling */
    .sidebar {
      width: 250px;
      background-color: #34495e;
      color: #fff;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      flex-direction: column;
      padding-top: 20px;
    }

    .sidebar h2 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 22px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar ul li {
      margin: 10px 0;
    }

    .sidebar ul li a {
      color: #fff;
      text-decoration: none;
      padding: 10px 20px;
      display: block;
      border-radius: 4px;
    }

    .sidebar ul li a:hover {
      background-color: #2c3e50;
    }

    /* Navbar Styling */
    .navbar {
      height: 60px;
      background-color: #f9f9f9;
      width: calc(100% - 250px);
      position: fixed;
      top: 0;
      left: 250px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .navbar .admin-info {
      font-size: 18px;
      font-weight: bold;
    }

    .navbar-logo {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
    }

    .logo {
      max-height: 70px;
      object-fit: contain;
    }

    .user-section {
      padding: 5px;
      margin-right: 40px; /* Adds spacing between the icon and other elements */
      position: relative; /* Ensure dropdown is positioned relative to the icon */
      display: inline-block; /* Ensures the user section remains compact */
      cursor: pointer; /* Indicate interactivity */
    }

.user-icon {
  width: 31px;
  height: 31px;
  border-radius: 50%;
  border: 2px solid #ddd;
  object-fit: cover;
}

.dropdown-content {
  display: none; /* Hidden initially */
  position: absolute;
  top: 40px; /* Position the dropdown just below the icon */
  left: 50%; /* Align the dropdown with the center of the icon */
  transform: translateX(-90%); /* Adjust for perfect horizontal centering */
  background-color: white;
  min-width: 150px;
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
  background-color: #dba475;
  color: white;
}

/* Show dropdown on hover */
.user-section:hover .dropdown-content {
  display: block; /* Make dropdown visible */
  opacity: 1; /* Fully visible */
  visibility: visible; /* Allow interaction */
}



    /* Main Content Styling */
    .main-content {
      margin-top: 60px;
      margin-left: 250px; /* Matches the sidebar width */
      padding: 20px;
      background-color: #f4f4f4;
      width: calc(100% - 250px); /* Full width minus sidebar */
      box-sizing: border-box;
    }

    .main-content h1 {
      font-size: 28px;
      margin-bottom: 20px;
    }

    .quick-actions, .featured-products, .newsletter, .user-reviews {
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .quick-actions ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .quick-actions ul li {
      background: #e67e22;
      color: #fff;
      padding: 10px 15px;
      border-radius: 5px;
      flex: 1 1 calc(33.333% - 10px);
      text-align: center;
    }

    .quick-actions ul li a {
      color: #fff;
      text-decoration: none;
    }

    .featured-products img {
      max-width: 100%;
      border-radius: 10px;
    }

    .newsletter textarea, .newsletter input[type="text"] {
      width: 100%;
      margin: 10px 0;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    .newsletter button {
      background: #27ae60;
      color: #fff;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .user-reviews ul {
      list-style: none;
      padding: 0;
    }

    .user-reviews ul li {
      margin-bottom: 10px;
      padding: 10px;
      background: #f9f9f9;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
      <li><a href="admindashboard.php">Dashboard</a></li>
      <li><a href="moderation.php">Moderation Panel</a></li>
      <li><a href="factMan.php">Food Facts</a></li>
      <li><a href="eventMan.php">Event Management</a></li>
    </ul>
  </div>

  <!-- Navbar -->
  <div class="navbar">
    <div class="admin-info">Welcome, <?php echo htmlspecialchars($adminName); ?></div>
    <form action="../php/logout.php" method="POST" style="margin: 0;">
  <button type="submit" style="background-color: #e74c3c; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; margin-right: 30px;">
    Log Out
  </button>
</form>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Dashboard</h1>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h2>Quick Actions</h2>
      <ul>
        <li><a href="#">Review Flagged Content</a></li>
        <li><a href="#">Assign User Roles</a></li>
        <li><a href="#">Analyze Engagement</a></li>
      </ul>
    </div>
  </div>
</body>
</html>
