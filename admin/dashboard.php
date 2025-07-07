<?php
require_once '../db.php';

$queryProducts = "SELECT COUNT(*) AS totalProducts FROM products";
$stmtProducts = $pdo->prepare($queryProducts);
$stmtProducts->execute();
$totalProducts = $stmtProducts->fetch(PDO::FETCH_ASSOC)['totalProducts'];

$queryOrders = "SELECT COUNT(*) AS totalOrders FROM orders";
$stmtOrders = $pdo->prepare($queryOrders);
$stmtOrders->execute();
$totalOrders = $stmtOrders->fetch(PDO::FETCH_ASSOC)['totalOrders'];

$queryUsers = "SELECT COUNT(*) AS totalUsers FROM users";
$stmtUsers = $pdo->prepare($queryUsers);
$stmtUsers->execute();
$totalUsers = $stmtUsers->fetch(PDO::FETCH_ASSOC)['totalUsers'];
?>

<h2 class="mb-4">Dashboard</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Tổng số sản phẩm</h5>
                <p class="card-text display-6">
                    <?= htmlspecialchars($totalProducts) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Tổng số đơn hàng</h5>
                <p class="card-text display-6">
                    <?= htmlspecialchars($totalOrders) ?>
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Tổng số người dùng</h5>
                <p class="card-text display-6">
                    <?= htmlspecialchars($totalUsers) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
