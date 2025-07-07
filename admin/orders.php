<?php
require_once '../db.php';

// Xử lý xóa đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $orderID = $_POST['OrderID'];
    
    try {
        // Bắt đầu transaction
        $pdo->beginTransaction();
        
        // Lấy OrderDetailID trước khi xóa orders
        $getDetailQuery = "SELECT OrderDetailID FROM orders WHERE OrderID = :OrderID";
        $stmt = $pdo->prepare($getDetailQuery);
        $stmt->execute([':OrderID' => $orderID]);
        $orderDetailID = $stmt->fetchColumn();
        
        // Xóa từ bảng orders trước
        $deleteOrderQuery = "DELETE FROM orders WHERE OrderID = :OrderID";
        $stmt = $pdo->prepare($deleteOrderQuery);
        $stmt->execute([':OrderID' => $orderID]);
        
        // Sau đó xóa từ bảng orderdetail
        if ($orderDetailID) {
            $deleteDetailQuery = "DELETE FROM orderdetail WHERE OrderDetailID = :OrderDetailID";
            $stmt = $pdo->prepare($deleteDetailQuery);
            $stmt->execute([':OrderDetailID' => $orderDetailID]);
        }
        
        header('Location: index.html#orders.php');
        exit;
        
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $pdo->rollBack();
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        } else {
            echo "Error: " . $e->getMessage();
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['OrderID'], $_POST['Status'])) {
    $orderID = $_POST['OrderID'];
    $status = $_POST['Status'];

    $updateQuery = "UPDATE orders SET Status = :Status WHERE OrderID = :OrderID";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([':Status' => $status, ':OrderID' => $orderID]);

    echo "<p class='text-success'>Cập nhật trạng thái thành công!</p>";
    header("Location: index.html#orders.php");
}

$query = "
    SELECT 
        o.OrderID, 
        u.Username, 
        o.Total, 
        o.Status
    FROM 
        orders o
    INNER JOIN 
        users u ON o.UserID = u.UserID
    INNER JOIN 
        orderdetail od ON o.OrderDetailID = od.OrderDetailID
    INNER JOIN 
        products p ON od.ProductID = p.ProductID
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Orders Page</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= htmlspecialchars($order['OrderID']) ?></td>
            <td><?= htmlspecialchars($order['Username']) ?></td>
            <td><?= htmlspecialchars(number_format($order['Total'], 2)) ?> $</td>
            <td>
                <form method="POST" action="orders.php" class="d-inline">
                    <input type="hidden" name="OrderID" value="<?= htmlspecialchars($order['OrderID']) ?>">
                    <select name="Status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="Placed Order" <?= $order['Status'] === 'Placed Order' ? 'selected' : '' ?>>Placed Order</option>
                        <option value="Shipping" <?= $order['Status'] === 'Shipping' ? 'selected' : '' ?>>Shipping</option>
                        <option value="Delivered" <?= $order['Status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option value="Cancel" <?= $order['Status'] === 'Cancel' ? 'selected' : '' ?>>Cancel</option>
                    </select>
                </form>
            </td>
            <td>
                <form method="POST" class="delete-form d-inline">
                    <input type="hidden" name="OrderID" value="<?= htmlspecialchars($order['OrderID']) ?>">
                    <button type="submit" name="delete_order" class="btn btn-danger btn-sm delete-btn"
                    onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
