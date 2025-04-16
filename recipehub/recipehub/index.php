<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Recipe Hub</title>
  <link rel="stylesheet" href="css/loginstyles.css">
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <form action="php/login.php" method="POST">
      <input type="text" class="input-field" name="username" placeholder="Username" required>
      <input type="password" class="input-field" name="password" placeholder="Password" required>
      <button type="submit" class="submit-btn">Login</button>
    </form>
    <p>Don't have an account? <a href="signup.html">Sign up here</a></p>
  </div>
</body>
</html>
