<div class="sidebar">
    <div class="sidebar-brand"><i class="fa-solid fa-boxes-stacked"></i> InventorySys</div>
    <ul class="sidebar-menu">
        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
        <li><a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>"><i class="fa-solid fa-box"></i> Products List</a></li>
        <li><a href="add_product.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>"><i class="fa-solid fa-plus-circle"></i> Add Product</a></li>
        <li><a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>
</div>

<style>
    /* Sidebar Logout Special Style */
    .sidebar-menu li a.logout-btn { color: #f87171 !important; }
    .sidebar-menu li a.logout-btn:hover { background: #7f1d1d !important; color: #fca5a5 !important; border-left: 4px solid #f87171 !important; }
</style>