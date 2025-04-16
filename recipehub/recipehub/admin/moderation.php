<?php
session_start(); // Start session handling

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

include '../php/db.php'; // Include the database connection file

$adminName = $_SESSION['username'];

// Handle recipe deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $recipeId = intval($_POST['recipe_id']);

    try {
        $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
        $stmt->bind_param("i", $recipeId);

        if ($stmt->execute()) {
            $message = "Recipe deleted successfully!";
        } else {
            $message = "Failed to delete recipe. Please try again.";
        }

        $stmt->close();
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch recipes from the database
$recipes = [];
try {
    $query = "SELECT id, name, ingredients, steps, media FROM recipes ORDER BY id DESC";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
} catch (Exception $e) {
    echo "Error fetching recipes: " . $e->getMessage();
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Moderation Panel</title>
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

    /* Main Content Styling */
    .main-content {
      margin-top: 60px;
      margin-left: 250px;
      padding: 20px;
      background-color: #f4f4f4;
      width: calc(100% - 250px);
      box-sizing: border-box;
    }

    .main-content h1 {
      font-size: 28px;
      margin-bottom: 20px;
    }

    .recipe-table {
      width: 100%;
      border-collapse: collapse;
      margin: 0 auto;
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .recipe-table th, .recipe-table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    .recipe-table th {
      background-color: #34495e;
      color: #ffffff;
    }

    .recipe-media img,
    .recipe-media video {
      max-width: 100px;
      max-height: 100px;
      display: block;
    }

    .delete-btn {
      background-color: #e74c3c;
      color: #ffffff;
      padding: 5px 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    .delete-btn:hover {
      background-color: #c0392b;
    }

    .message {
      color: green;
      text-align: center;
      margin-bottom: 20px;
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
    <h1>Moderation Panel</h1>
    <?php if (isset($message)): ?>
      <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <table class="recipe-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Ingredients</th>
          <th>Steps</th>
          <th>Media</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($recipes)): ?>
          <?php foreach ($recipes as $recipe): ?>
            <tr>
              <td><?php echo htmlspecialchars($recipe['name']); ?></td>
              <td><?php echo htmlspecialchars($recipe['ingredients']); ?></td>
              <td><?php echo htmlspecialchars($recipe['steps']); ?></td>
              <td class="recipe-media">
  <?php if (!empty($recipe['media']) && file_exists("../client/" . $recipe['media'])): ?>
    <?php $fileExtension = pathinfo($recipe['media'], PATHINFO_EXTENSION); ?>
    <?php if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
      <img src="../client/<?php echo htmlspecialchars($recipe['media']); ?>" alt="Recipe Media">
    <?php elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])): ?>
      <video controls>
        <source src="../client/<?php echo htmlspecialchars($recipe['media']); ?>" type="video/<?php echo $fileExtension; ?>">
      </video>
    <?php endif; ?>
  <?php else: ?>
    No Media
  <?php endif; ?>
</td>


              <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this recipe?');">
                  <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                  <button type="submit" name="delete" class="delete-btn">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" style="text-align: center;">No recipes found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
