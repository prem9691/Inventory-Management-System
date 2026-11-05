<?php
session_start();
require_once "config/database.php";
require_once "functions.php";

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ? OR category LIKE ? OR supplier LIKE ? ORDER BY id DESC");
    $searchTerm = "%" . $search . "%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Products - InventorySys</title>
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
        .content { padding: 30px; }
        
        .table-card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px; overflow-x: auto; }
        
        .table-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 15px; flex-wrap: wrap; }
        .search-box { display: flex; gap: 10px; }
        .search-box input { padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 0.9rem; width: 240px; }
        .search-box input:focus { border-color: #38bdf8; }
        .btn-search { background: #0284c7; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500; }
        .btn-search:hover { background: #0369a1; }
        .btn-reset { background: #e2e8f0; color: #334155; text-decoration: none; padding: 8px 14px; border-radius: 6px; font-size: 0.9rem; font-weight: 500; display: inline-flex; align-items: center; }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        th { background: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; }
        tr:hover { background: #f8fafc; }
        .badge { background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-weight: 500; font-size: 0.8rem; color: #334155; }
        .badge-low { background: #fee2e2; color: #dc2626; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        .badge-normal { background: #dcfce7; color: #16a34a; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        
        .btn-edit { background: #e0f2fe; color: #0284c7; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; margin-right: 5px; display: inline-block; }
        .btn-edit:hover { background: #bae6fd; }
        .btn-delete { background: #fee2e2; color: #dc2626; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; display: inline-block; }
        .btn-delete:hover { background: #fca5a5; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-boxes-stacked"></i> InventorySys</div>
        <ul class="sidebar-menu">
            <li><a href="index.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="products.php" class="active"><i class="fa-solid fa-box"></i> Products List</a></li>
            <li><a href="add_product.php"><i class="fa-solid fa-plus-circle"></i> Add Product</a></li>
            <li><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-container">
        <div class="topbar">
            <h2>All Inventory Products</h2>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
        </div>
        <div class="content">
            <?php display_flash_message(); ?>
            <div class="table-card">
                
                <div class="table-header-flex">
                    <h3 style="color: #1e293b;">Product Inventory</h3>
                    <form method="GET" class="search-box">
                        <input type="text" name="search" placeholder="Search product, category..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn-search"><i class="fa-solid fa-search"></i> Search</button>
                        <?php if(!empty($search)): ?>
                            <a href="products.php" class="btn-reset">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Supplier</th>
                            <th>Stock Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($products) > 0): ?>
                            <?php foreach($products as $p): ?>
                            <tr>
                                <td>#<?php echo $p['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($p['product_name']); ?></strong></td>
                                <td><span class="badge"><?php echo htmlspecialchars($p['category']); ?></span></td>
                                <td><?php echo $p['quantity']; ?></td>
                                <td style="color: #16a34a; font-weight: 600;">₹<?php echo number_format($p['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($p['supplier']); ?></td>
                                <td>
                                    <?php if($p['quantity'] <= 10): ?>
                                        <span class="badge-low"><i class="fa-solid fa-circle-exclamation"></i> Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge-normal">In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                    <a href="delete_product.php?id=<?php echo $p['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fa-solid fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: #64748b; padding: 30px;">No products found!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>