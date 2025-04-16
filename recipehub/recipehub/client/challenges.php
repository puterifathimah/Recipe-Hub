<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

include '../php/db.php'; // Include the database connection file

// Fetch approved challenges
$sql = "SELECT id, title, description, image, start_date, end_date 
        FROM challenges 
        WHERE status = 'approved' AND end_date >= CURDATE()";
$result = $conn->query($sql);

$challenges = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $challenges[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Community Challenges</title>
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
        <li><a href="challenges.php">Community Challenges</a></li>
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
  <section class="challenges-container">
    <?php if (empty($challenges)): ?>
      <p>No challenges are available at the moment. Check back later!</p>
    <?php else: ?>
      <?php foreach ($challenges as $challenge): ?>
        <div class="challenge-card">
          <?php if ($challenge['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($challenge['image']); ?>" alt="<?php echo htmlspecialchars($challenge['title']); ?>">
          <?php endif; ?>
          <div class="challenge-content">
            <h2><?php echo htmlspecialchars($challenge['title']); ?></h2>
            <p><?php echo htmlspecialchars($challenge['description']); ?></p>
            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($challenge['start_date']); ?></p>
            <p><strong>End Date:</strong> <?php echo htmlspecialchars($challenge['end_date']); ?></p>
            <a href="register-challenge.php?challenge_id=<?php echo $challenge['id']; ?>" class="register-button">Register Now</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
</main>

<footer>
  <p>© 2022 RecipeHub. All Rights Reserved.</p>
</footer>
</body>
</html>
