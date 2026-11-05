<?php
session_start();
require_once "config/database.php";
require_once "functions.php";

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalStock = $conn->query("SELECT COALESCE(SUM(quantity), 0) FROM products")->fetchColumn();

// Total Inventory Value (Price * Quantity ka sum)
$totalValue = $conn->query("SELECT COALESCE(SUM(price * quantity), 0) FROM products")->fetchColumn();

// Low stock items count (Quantity <= 10)
$lowStockCount = $conn->query("SELECT COUNT(*) FROM products WHERE quantity <= 10")->fetchColumn();

$recentProducts = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f1f5f9; display: flex; height: 100vh; overflow: hidden; }
        
        .sidebar { width: 250px; background: #0f172a; color: white; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 25px 20px; font-size: 1.2rem; font-weight: 700; letter-spacing: 0.5px; border-bottom: 1px solid #1e293b; color: #38bdf8; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex: 1; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #94a3b8; text-decoration: none; font-weight: 500; transition: all 0.3s ease; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background: #1e293b; color: #38bdf8; border-left: 4px solid #38bdf8; }
        
        .logout-btn { color: #f87171 !important; }
        .logout-btn:hover { background: #7f1d1d !important; color: #fca5a5 !important; border-left: 4px solid #f87171 !important; }

        .main-container { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        .topbar { background: white; padding: 15px 30px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .topbar h2 { font-size: 1.3rem; color: #1e293b; }
        
        .content { padding: 30px; }
        
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 20px; }
        .card-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .card-icon.blue { background: #e0f2fe; color: #0284c7; }
        .card-icon.green { background: #dcfce7; color: #16a34a; }
        .card-icon.purple { background: #f3e8ff; color: #9333ea; }
        .card-icon.red { background: #fee2e2; color: #dc2626; }
        
        .card-info h3 { font-size: 0.8rem; color: #64748b; font-weight: 500; text-transform: uppercase; }
        .card-info h2 { font-size: 1.4rem; color: #0f172a; margin-top: 5px; }

        .table-card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; padding: 20px; }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .table-header h3 { font-size: 1.1rem; color: #1e293b; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        th { background: #f8fafc; color: #475569; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; }
        tr:hover { background: #f8fafc; }
        .badge { background: #f1f5f9; padding: 4px 10px; border-radius: 20px; font-weight: 500; font-size: 0.8rem; color: #334155; }
        .badge-low { background: #fee2e2; color: #dc2626; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        .badge-normal { background: #dcfce7; color: #16a34a; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fa-solid fa-boxes-stacked"></i> InventorySys</div>
        <ul class="sidebar-menu">
            <li><a href="index.php" class="active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fa-solid fa-box"></i> Products List</a></li>
            <li><a href="add_product.php"><i class="fa-solid fa-plus-circle"></i> Add Product</a></li>
            <li><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-container">
        <div class="topbar">
            <h2>Dashboard Overview</h2>
            <span style="color: #64748b; font-size: 0.9rem;"><i class="fa-solid fa-user-shield"></i> Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
        </div>

        <div class="content">
            <?php display_flash_message(); ?>
            
            <!-- Statistics Cards -->
            <div class="cards">
                <div class="card">
                    <div class="card-icon blue"><i class="fa-solid fa-cubes"></i></div>
                    <div class="card-info">
                        <h3>Total Products</h3>
                        <h2><?php echo $totalProducts; ?></h2>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon green"><i class="fa-solid fa-warehouse"></i></div>
                    <div class="card-info">
                        <h3>Total Stock Qty</h3>
                        <h2><?php echo $totalStock; ?></h2>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon purple"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                    <div class="card-info">
                        <h3>Inventory Value</h3>
                        <h2>₹<?php echo number_format($totalValue, 2); ?></h2>
                    </div>
                </div>
                <div class="card">
                    <div class="card-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div class="card-info">
                        <h3>Low Stock Items</h3>
                        <h2><?php echo $lowStockCount; ?></h2>
                    </div>
                </div>
            </div>

            <!-- Recent Products Table -->
            <div class="table-card">
                <div class="table-header">
                    <h3>Recent Database Products</h3>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recentProducts as $p): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($p['product_name']); ?></strong></td>
                            <td><span class="badge"><?php echo htmlspecialchars($p['category']); ?></span></td>
                            <td><?php echo $p['quantity']; ?></td>
                            <td style="color: #16a34a; font-weight: 600;">₹<?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <?php if($p['quantity'] <= 10): ?>
                                    <span class="badge-low"><i class="fa-solid fa-circle-exclamation"></i> Low Stock</span>
                                <?php else: ?>
                                    <span class="badge-normal">In Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>