<?php
session_start();
require_once "config/database.php";
require_once "functions.php";

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if ($stmt->execute([$id])) {
        set_flash_message("Product deleted successfully!", "success");
    } else {
        set_flash_message("Failed to delete product!", "error");
    }
}

header("Location: products.php");
exit();
?>