<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'client') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/dashboardstyles.css">
  <title>RecipeHub Dashboard</title>
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
    <section class="hero">
      <div class="hero-text">
        <h1>Welcome to Recipe Hub</h1>
        <p>
          "Every recipe is more than food—<br>
          It's love, creativity, and a little adventure. Let's mix, taste, and enjoy the journey."<br>
          <em>~Happy Cooking~</em>
        </p>
      </div>
    </section>

    <section class="quick-access">
      <h2>Quick Access</h2>
      <div class="quick-access-items">
        <div class="quick-access-item">
          <a href="foodfacts.php">
            <img src="../images/view.png" alt="Food Facts">
          </a>
          <p>Food Facts</p>
        </div>

        <div class="quick-access-item">
          <a href="addrecipe.php">
            <img src="../images/add.png" alt="Add Recipe">
          </a>
          <p>Add Recipe</p>
        </div>

        <div class="quick-access-item">
          <a href="mealplanner.php">
            <img src="../images/mealplan.png" alt="Meal Planner">
          </a>
          <p>Meal Planner</p>
        </div>

        <div class="quick-access-item">
          <a href="recipecollection.php">
            <img src="../images/collection.png" alt="Recipe Collection">
          </a>
          <p>Recipe Collection</p>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <p>© 2022 RecipeHub. All Rights Reserved.</p>
  </footer>

  <button class="scroll-top" onclick="scrollToTop()">
    <img src="../images/up-arrow.png" alt="Scroll to top">
  </button>

  <script src="../js/scripts.js"></script>
</body>
</html>
