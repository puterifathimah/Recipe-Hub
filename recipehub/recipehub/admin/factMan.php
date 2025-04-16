<?php
session_start(); // Start session handling

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrator') {
    header("Location: login.php"); // Redirect to login page if not logged in or not an admin
    exit();
}

include '../php/db.php'; // Include the database connection file

$adminName = $_SESSION['username'];

// Initialize variables
$editPost = null;

// Fetch the post data if editing
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $editPost = $result->fetch_assoc();
    }
    $stmt->close();
}

// Handle Create and Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $postId = $_POST['id'] ?? null;

    // Handle Image Upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'uploads/';
        $fileName = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Ensure the upload directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Create the directory
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $imagePath = 'uploads/' . $fileName;
        } else {
            echo "Error: Unable to upload the file.";
            exit();
        }
    }

    // Insert a new post (Create)
    if (empty($postId)) {
        $stmt = $conn->prepare("INSERT INTO posts (title, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $imagePath);
        $stmt->execute();
        $stmt->close();
    } else {
        // Update an existing post (Update)
        if (!empty($imagePath)) {
            $stmt = $conn->prepare("UPDATE posts SET title = ?, description = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $description, $imagePath, $postId);
        } else {
            $stmt = $conn->prepare("UPDATE posts SET title = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $title, $description, $postId);
        }
        $stmt->execute();
        $stmt->close();
    }

    header("Location: factMan.php");
    exit();
}

// Handle Delete
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $postId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $stmt->close();

    header("Location: factMan.php");
    exit();
}

// Fetch all posts (Read)
$posts = [];
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Facts Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/omj0kkiub5u1s42s7kziqgr580o7iaxhadmwegmbsw58fyuu/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        // Initialize TinyMCE for the description field
        tinymce.init({
            selector: '#description',
            plugins: 'link image code lists',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image',
            menubar: false
        });

        // Sync TinyMCE content with textarea before submitting the form
        function syncTinyMCEContent() {
            if (tinymce.get('description')) {
                tinymce.get('description').save(); // Save the TinyMCE content into the textarea
            }
        }
    </script>
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


        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #34495e;
            color: white;
        }

        .delete-btn, .edit-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .edit-btn {
            background-color: #3498db;
        }

        .form-container {
            margin-top: 20px;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input[type="text"], textarea, input[type="file"], button[type="submit"] {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #218838;
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
        <button type="submit" 
                style="
                    background-color: #e74c3c; 
                    color: white; 
                    border: none; 
                    padding: 10px 15px; 
                    border-radius: 5px; 
                    cursor: pointer; 
                    font-weight: bold; 
                    margin-right: 30px; /* Adjusted for moving the button left */
                ">
            Log Out
        </button>
    </form>
</div>


    <div class="main-content">
        <h1>Manage Food Facts</h1>
        
        <div class="form-container">
            <h2><?php echo isset($_GET['edit']) ? 'Edit Post' : 'Add New Post'; ?></h2>
            <form method="POST" enctype="multipart/form-data" onsubmit="syncTinyMCEContent()">
                <?php if (isset($editPost)): ?>
                    <input type="hidden" name="id" value="<?php echo $editPost['id']; ?>">
                <?php endif; ?>
                <input type="text" name="title" placeholder="Post Title" value="<?php echo $editPost['title'] ?? ''; ?>" required>
                <textarea id="description" name="description" rows="5"><?php echo $editPost['description'] ?? ''; ?></textarea>
                <?php if (isset($editPost['image'])): ?>
                    <p>Current Image:</p>
                    <img src="../admin/<?php echo htmlspecialchars($editPost['image']); ?>" alt="Post Image" width="100">
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
                <button type="submit"><?php echo isset($editPost) ? 'Update Post' : 'Add Post'; ?></button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars(strip_tags($post['description'])); ?></td>
                        <td><img src="../admin/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" width="100"></td>
                        <td>
                            <a href="factMan.php?edit=<?php echo $post['id']; ?>" class="edit-btn">Edit</a>
                            <a href="factMan.php?delete=<?php echo $post['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
