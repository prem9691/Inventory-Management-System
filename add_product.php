<?php
session_start();
require_once "config/database.php";
require_once "functions.php";

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = trim($_POST['product_name']);
    $category = trim($_POST['category']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $supplier = trim($_POST['supplier']);

    if (!empty($product_name) && !empty($category)) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, category, quantity, price, supplier) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$product_name, $category, $quantity, $price, $supplier])) {
            set_flash_message("Product added successfully!", "success");
            header("Location: products.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product - InventorySys</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 250px; background: #0f172a; color: white; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 25px 20px; font-size: 1.2rem; font-weight: 700; color: #38bdf8; border-bottom: 1px solid #1e293b; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex: 1; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #94a3b8; text-decoration: none; font-weight: 500; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: #1e293b; color: #38bdf8; border-left: 4px solid #38bdf8; }
        .logout-btn { color: #f87171 !important; }

        .main-container { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .topbar { background: white; padding: 15px 30px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .content { padding: 30px; max-width: 700px; }
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #475569; font-weight: 500; font-size: 0.9rem; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; outline: none; }
        .form-group input:focus { border-color: #38bdf8; }
        .btn-submit { background: #0284c7; color: white; padding: 12px 20px; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; width: 100%; }
        .btn-submit:hover { background: #0369a1; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-boxes-stacked"></i> InventorySys</div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fa-solid fa-box"></i> Products List</a></li>
            <li><a href="add_product.php" class="active"><i class="fa-solid fa-plus-circle"></i> Add Product</a></li>
            <li><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-container">
        <div class="topbar">
            <h2>Add New Product</h2>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
        </div>
        <div class="content">
            <div class="form-card">
                <?php display_flash_message(); ?>
                <form method="POST">
                    <div class="form-group"><label>Product Name</label><input type="text" name="product_name" required></div>
                    <div class="form-group"><label>Category</label><input type="text" name="category" required></div>
                    <div class="form-group"><label>Quantity</label><input type="number" name="quantity" required></div>
                    <div class="form-group"><label>Price (₹)</label><input type="number" step="0.01" name="price" required></div>
                    <div class="form-group"><label>Supplier</label><input type="text" name="supplier"></div>
                    <button type="submit" class="btn-submit">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>