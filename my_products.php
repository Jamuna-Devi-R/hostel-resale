<?php
require 'config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mark as sold
if(isset($_GET['sold'])) {
    $stmt = $pdo->prepare("UPDATE products SET status='sold' WHERE id=? AND user_id=?");
    $stmt->execute([$_GET['sold'], $_SESSION['user_id']]);
    header('Location: my_products.php');
}

// Delete product
if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=? AND user_id=?");
    $stmt->execute([$_GET['delete'], $_SESSION['user_id']]);
    header('Location: my_products.php');
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();
// Get interests for each product
$interest_counts = [];
$interests_list = [];
foreach($products as $p) {
    $int_stmt = $pdo->prepare("SELECT interests.*, users.name as buyer_name, users.email as buyer_email FROM interests JOIN users ON interests.buyer_id = users.id WHERE interests.product_id = ? AND interests.status = 'pending'");
    $int_stmt->execute([$p['id']]);
    $interests_list[$p['id']] = $int_stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Items - Hostel Resale</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }
        .navbar { background: #4CAF50; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { color: white; font-size: 24px; }
        .navbar a { color: white; text-decoration: none; margin-left: 15px; padding: 8px 15px; border-radius: 5px; background: rgba(255,255,255,0.2); }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        h2 { color: #333; margin-bottom: 25px; }
        .product-table { width: 100%; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #4CAF50; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background: #f9f9f9; }
        .status-available { color: #4CAF50; font-weight: bold; }
        .status-sold { color: #f44336; font-weight: bold; }
        .btn { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; margin-right: 5px; }
        .btn-sold { background: #FF9800; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .btn-view { background: #2196F3; color: white; }
        .no-products { text-align: center; padding: 50px; color: #666; }
        img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🏠 Hostel Resale</h1>
        <div>
            <a href="index.php">Home</a>
            <a href="sell.php">+ Sell Item</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>📦 My Listed Items</h2>
        <?php if(count($products) > 0): ?>
        <div class="product-table">
            <table>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php foreach($products as $product): ?>
                <tr>
                    <td>
                        <?php if($product['image']): ?>
                            <img src="uploads/<?php echo $product['image']; ?>" alt="">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/60?text=No+Img" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?php echo $product['title']; ?></td>
                    <td>₹<?php echo $product['price']; ?></td>
                    <td><?php echo $product['category']; ?></td>
                    <td>
                        <?php if($product['status'] == 'available'): ?>
                            <span class="status-available">✅ Available</span>
                        <?php else: ?>
                            <span class="status-sold">❌ Sold</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-view">View</a>
                        <?php if($product['status'] == 'available'): ?>
                            <a href="my_products.php?sold=<?php echo $product['id']; ?>" class="btn btn-sold" onclick="return confirm('Mark as sold?')">Mark Sold</a>
                        <?php endif; ?>
                        <a href="my_products.php?delete=<?php echo $product['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this item?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php if(count($interests_list[$product['id']]) > 0): ?>
    <div style="margin-top:10px;">
        <strong>🔔 Interested Buyers:</strong>
        <?php foreach($interests_list[$product['id']] as $buyer): ?>
            <div style="background:#fff3e0;padding:8px;border-radius:5px;margin-top:5px;">
                👤 <?php echo $buyer['buyer_name']; ?> 
                📧 <?php echo $buyer['buyer_email']; ?>
                <a href="my_products.php?sold=<?php echo $product['id']; ?>" 
                   style="background:#4CAF50;color:white;padding:4px 8px;border-radius:3px;text-decoration:none;margin-left:10px;font-size:12px;">
                   ✅ Confirm Sale
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
        </div>
        <?php else: ?>
            <div class="no-products">
                <p>No items listed yet! <a href="sell.php">Start selling!</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>