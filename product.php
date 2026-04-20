<?php
require 'config.php';
session_start();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT products.*, users.name as seller_name, users.email as seller_email, users.hostel_name FROM products JOIN users ON products.user_id = users.id WHERE products.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if(!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $product['title']; ?> - Hostel Resale</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        .navbar { background: #4CAF50; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { color: white; font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 15px; padding: 8px 15px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .container { max-width: 900px; margin: 40px auto; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; display: grid; grid-template-columns: 1fr 1fr; }
        .product-image img { width: 100%; height: 400px; object-fit: cover; }
        .product-details { padding: 30px; }
        .product-details h2 { color: #333; font-size: 24px; margin-bottom: 15px; }
        .price { color: #4CAF50; font-size: 32px; font-weight: bold; margin-bottom: 15px; }
        .category { background: #e8f5e9; color: #4CAF50; padding: 5px 12px; border-radius: 3px; font-size: 14px; display: inline-block; margin-bottom: 15px; }
        .description { color: #666; line-height: 1.6; margin-bottom: 20px; }
        .seller-info { background: #f5f5f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .seller-info h3 { color: #333; margin-bottom: 10px; }
        .seller-info p { color: #666; margin: 5px 0; }
        .contact-btn { display: block; text-align: center; padding: 15px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; margin-bottom: 10px; }
        .contact-btn:hover { background: #45a049; }
        .back-btn { display: block; text-align: center; padding: 12px; background: #2196F3; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; }
        .sold-badge { background: #f44336; color: white; padding: 5px 12px; border-radius: 3px; font-size: 14px; display: inline-block; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🏠 Hostel Resale</h1>
        <div>
            <a href="index.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="sell.php">+ Sell Item</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
        <div class="product-image">
            <?php if($product['image']): ?>
                <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['title']; ?>">
            <?php else: ?>
                <img src="https://via.placeholder.com/450x400?text=No+Image" alt="No Image">
            <?php endif; ?>
        </div>
        <div class="product-details">
            <h2><?php echo $product['title']; ?></h2>
            <?php if($product['status'] == 'sold'): ?>
                <span class="sold-badge">❌ SOLD</span>
            <?php else: ?>
                <span class="category">✅ Available</span>
            <?php endif; ?>
            <p class="price">₹<?php echo $product['price']; ?></p>
            <span class="category"><?php echo $product['category']; ?></span>
            <p class="description"><?php echo $product['description']; ?></p>
            <div class="seller-info">
                <h3>👤 Seller Details</h3>
                <p>📛 Name: <?php echo $product['seller_name']; ?></p>
                <p>🏠 Hostel: <?php echo $product['hostel_name']; ?></p>
                <p>📧 Email: <?php echo $product['seller_email']; ?></p>
            </div>
            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != $product['user_id']): ?>
    <?php if($product['status'] == 'available'): ?>
      <?php
$interest_stmt = $pdo->prepare("SELECT * FROM interests WHERE product_id=? AND buyer_id=?");
$interest_stmt->execute([$product['id'], $_SESSION['user_id'] ?? 0]);
$already_interested = $interest_stmt->fetch();

if(isset($_POST['interested'])) {
    if(!$already_interested) {
        $ins = $pdo->prepare("INSERT INTO interests (product_id, buyer_id) VALUES (?,?)");
        $ins->execute([$product['id'], $_SESSION['user_id']]);
        header('Location: product.php?id='.$product['id']);
    }
}
?>
<form method="POST">
    <?php if($already_interested): ?>
        <button style="width:100%;padding:15px;background:#FF9800;color:white;border:none;border-radius:5px;font-size:16px;cursor:not-allowed;">⏳ Already Requested!</button>
    <?php else: ?>
        <button type="submit" name="interested" class="contact-btn" style="border:none;cursor:pointer;">🛒 I'm Interested - Buy This!</button>
    <?php endif; ?>
</form>
    <?php else: ?>
        <button style="width:100%;padding:15px;background:#ccc;color:#666;border:none;border-radius:5px;font-size:16px;cursor:not-allowed;">❌ Item Already Sold</button>
    <?php endif; ?>
<?php elseif(!isset($_SESSION['user_id'])): ?>
    <a href="login.php" class="contact-btn">🔑 Login to Buy</a>
<?php endif; ?>
            <a href="index.php" class="back-btn">← Back to Products</a>
        </div>
    </div>
</body>
</html>