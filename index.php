<?php
require 'config.php';
session_start();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$query = "SELECT products.*, users.name as seller_name, users.hostel_name FROM products JOIN users ON products.user_id = users.id WHERE products.status = 'available'";

if($search) {
    $query .= " AND (products.title LIKE '%$search%' OR products.description LIKE '%$search%')";
}
if($category) {
    $query .= " AND products.category = '$category'";
}

$query .= " ORDER BY products.created_at DESC";
$products = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hostel Resale - Buy & Sell</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        .navbar { background: #4CAF50; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { color: white; font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 15px; padding: 8px 15px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .navbar a:hover { background: rgba(255,255,255,0.3); }
        .search-bar { padding: 20px 30px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .search-bar form { display: flex; gap: 10px; }
        .search-bar input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .search-bar select { padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .search-bar button { padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .products { padding: 30px; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .product-card img { width: 100%; height: 200px; object-fit: cover; }
        .product-info { padding: 15px; }
        .product-info h3 { color: #333; margin-bottom: 8px; }
        .product-info .price { color: #4CAF50; font-size: 20px; font-weight: bold; }
        .product-info .seller { color: #666; font-size: 13px; margin-top: 5px; }
        .product-info .category { background: #e8f5e9; color: #4CAF50; padding: 3px 8px; border-radius: 3px; font-size: 12px; display: inline-block; margin-top: 5px; }
        .product-info a { display: block; text-align: center; margin-top: 10px; padding: 8px; background: #2196F3; color: white; text-decoration: none; border-radius: 5px; }
        .no-products { text-align: center; padding: 50px; color: #666; font-size: 18px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🏠 Hostel Resale</h1>
        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="sell.php">+ Sell Item</a>
                <a href="my_products.php">My Items</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Books" <?php if($category=='Books') echo 'selected'; ?>>Books</option>
                <option value="Electronics" <?php if($category=='Electronics') echo 'selected'; ?>>Electronics</option>
                <option value="Clothes" <?php if($category=='Clothes') echo 'selected'; ?>>Clothes</option>
                <option value="Furniture" <?php if($category=='Furniture') echo 'selected'; ?>>Furniture</option>
                <option value="Others" <?php if($category=='Others') echo 'selected'; ?>>Others</option>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="products">
        <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
                <div class="product-card">
                    <?php if($product['image']): ?>
                        <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/280x200?text=No+Image" alt="No Image">
                    <?php endif; ?>
                    <div class="product-info">
                        <h3><?php echo $product['title']; ?></h3>
                        <p class="price">₹<?php echo $product['price']; ?></p>
                        <p class="seller">👤 <?php echo $product['seller_name']; ?> | 🏠 <?php echo $product['hostel_name']; ?></p>
                        <span class="category"><?php echo $product['category']; ?></span>
                        <a href="<?php echo isset($_SESSION['user_id']) ? 'product.php?id='.$product['id'] : 'login.php'; ?>">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products">
                <p>No products found! <a href="sell.php">Be the first to sell!</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>