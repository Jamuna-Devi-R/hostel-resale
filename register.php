<?php
require 'config.php';
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $hostel_name = $_POST['hostel_name'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, hostel_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $hostel_name]);
        header('Location: login.php');
    } catch(PDOException $e) {
        $error = "Email already exists!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Hostel Resale</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        button { width: 100%; padding: 12px; background: #4CAF50; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #45a049; }
        .login-link { text-align: center; margin-top: 15px; }
        .login-link a { color: #4CAF50; text-decoration: none; }
        .error { color: red; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🏠 Hostel Resale</h2>
        <h3 style="text-align:center; color:#666; margin-bottom:20px;">Create Account</h3>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="hostel_name" placeholder="Hostel Name" required>
            <button type="submit">Register</button>
        </form>
        <div class="login-link">
            <p>Already have account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>