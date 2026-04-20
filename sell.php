<?php
require 'config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image = '';

    if($_FILES['image']['name']) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
    }

    $stmt = $pdo->prepare("INSERT INTO products (user_id, title, description, price, category, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $description, $price, $category, $image]);
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sell Item - Hostel Resale</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        .navbar { background: #4CAF50; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { color: white; font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 15px; padding: 8px 15px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .container { max-width: 600px; margin: 40px auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 25px; }
        input, textarea, select { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        textarea { height: 120px; resize: vertical; }
        button { width: 100%; padding: 12px; background: #4CAF50; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #45a049; }
        label { color: #666; font-size: 14px; margin-top: 10px; display: block; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🏠 Hostel Resale</h1>
        <div>
            <a href="index.php">Home</a>
            <a href="my_products.php">My Items</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>📦 Sell Your Item</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Item Title</label>
            <input type="text" name="title" placeholder="Ex: Physics Textbook" required>
            <label>Description</label>
            <textarea name="description" placeholder="Describe your item..."></textarea>
            <label>Price (₹)</label>
            <input type="number" name="price" placeholder="Enter price" required>
            <label>Category</label>
            <select name="category">
                <option value="Books">Books</option>
                <option value="Electronics">Electronics</option>
                <option value="Clothes">Clothes</option>
                <option value="Furniture">Furniture</option>
                <option value="Others">Others</option>
            </select>
            <label>Product Image (Optional)</label>
            <input type="file" name="image" accept="image/*">
            <button type="submit">List Item for Sale</button>
        </form>
    </div>
</body>
</html>