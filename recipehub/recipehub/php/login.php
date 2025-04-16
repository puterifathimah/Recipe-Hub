<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user details from the database
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store user details in the session
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'client') {
                header("Location: ../client/dashboard.php");
                exit();
            } elseif ($user['role'] == 'organizer') {
                header("Location: ../organizer/organizerdashboard.php");
                exit();
            } elseif ($user['role'] == 'administrator') {
                header("Location: ../admin/admindashboard.php");
                exit();
            }
        } else {
            // Invalid password
            echo "<script>alert('Invalid password. Please try again.'); window.location.href='../index.html';</script>";
        }
    } else {
        // User not found
        echo "<script>alert('User not found. Please register first.'); window.location.href='../signup.html';</script>";
    }
}

$conn->close();
?>
