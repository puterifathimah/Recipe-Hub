<?php
include '../php/db.php'; // Include the database connection

$response = [];

try {
  // Query to fetch recipes from the database
  $query = "SELECT id, name, ingredients, steps, media, likes FROM recipes ORDER BY created_at DESC";
  $result = $conn->query($query);

  if ($result->num_rows > 0) {
      // Fetch each recipe and add it to the response array
      while ($row = $result->fetch_assoc()) {
          $response[] = [
              'id' => $row['id'],
              'name' => $row['name'],
              'ingredients' => nl2br($row['ingredients']), // Preserve line breaks
              'steps' => nl2br($row['steps']),             // Preserve line breaks
              'media' => $row['media'] ? $row['media'] : null, // Include media if available
              'likes' => $row['likes'] ?? 0 // Include likes, default to 0 if not set
          ];
      }
  }
} catch (Exception $e) {
  echo "Error fetching recipes: " . $e->getMessage();
  exit;
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recipe Collection</title>
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

    .recipes-container {
      max-width: 1200px;
      margin: 20px auto;
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      padding: 20px;
    }

    .recipe-card {
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .recipe-card img,
    .recipe-card video {
      width: 100%;
      max-height: 200px;
      object-fit: cover;
    }

    .recipe-content {
      padding: 15px;
    }

    .recipe-content h2 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    .like-btn {
  background-color: #e67e22;
  color: white;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.like-btn:hover {
  background-color: #d35400;
}

.like-btn:disabled {
  background-color: #bdc3c7;
  cursor: not-allowed;
}

.like-animation {
  animation: pop 0.6s ease;
  font-size: 24px;
}

@keyframes pop {
  0% {
    transform: scale(1);
    opacity: 0;
  }
  50% {
    transform: scale(1.5);
    opacity: 1;
  }
  100% {
    transform: scale(1);
    opacity: 0;
  }
}

    footer {
      text-align: center;
      background-color: #f9f9f9;
      padding: 10px 0;
      border-top: 1px solid #ddd;
      margin-top: 20px;
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
  <h1>Recipe Collection</h1>
  <div class="recipes-container">
    <?php if (!empty($response)): ?>
      <?php foreach ($response as $recipe): ?>
        <div class="recipe-card">
          <?php if ($recipe['media']): ?>
            <?php $fileExtension = pathinfo($recipe['media'], PATHINFO_EXTENSION); ?>
            <?php if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
              <img src="<?php echo htmlspecialchars($recipe['media']); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>">
            <?php elseif (in_array($fileExtension, ['mp4', 'webm', 'ogg'])): ?>
              <video controls>
                <source src="<?php echo htmlspecialchars($recipe['media']); ?>" type="video/<?php echo $fileExtension; ?>">
                Your browser does not support the video tag.
              </video>
            <?php endif; ?>
          <?php endif; ?>

          <div class="recipe-content">
  <h2><?php echo htmlspecialchars($recipe['name']); ?></h2>
  <p><strong>Ingredients:</strong><br><?php echo $recipe['ingredients']; ?></p>
  <p><strong>Steps:</strong><br><?php echo $recipe['steps']; ?></p>
  <div class="recipe-actions">
    <button class="like-btn" onclick="likeRecipe(<?php echo $recipe['id']; ?>, this)">
      👍 Like (<span class="likes-count"><?php echo $recipe['likes'] ?? 0; ?></span>)
    </button>
  </div>
</div>

        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No recipes found. Add some recipes to get started!</p>
    <?php endif; ?>
  </div>
</main>

<footer>
  <p>© 2022 RecipeHub. All Rights Reserved.</p>
</footer>

</body>
<script>
async function likeRecipe(recipeId, button) {
  try {
    const response = await fetch('like-recipe.php', {
      method: 'POST',
      body: JSON.stringify({ recipe_id: recipeId }),
      headers: { 'Content-Type': 'application/json' },
    });

    if (response.ok) {
      const result = await response.json();
      const likesCountSpan = button.querySelector('.likes-count');

      // Update the likes count
      likesCountSpan.textContent = result.likes;

      // Add animation to the button
      button.innerHTML = `<span class="like-animation">❤️</span> Liked (<span class="likes-count">${result.likes}</span>)`;
      button.disabled = true;

      // Reset the button after animation
      setTimeout(() => {
        button.innerHTML = `👍 Like (<span class="likes-count">${result.likes}</span>)`;
        button.disabled = false;
      }, 1500);
    } else {
      console.error('Failed to like the recipe.');
    }
  } catch (error) {
    console.error('Error liking the recipe:', error);
  }
}
</script>

</html>
