<?php
session_start();
require_once 'connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $cart = json_decode($_POST['cart'], true); // Giỏ hàng từ localStorage
    $status = 'Pending'; // Trạng thái đơn hàng mặc định

    if (!$userId || empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }

    try {
        $db = new Connection();
        $pdo = $db->getConnection();

        // Bắt đầu transaction
        $pdo->beginTransaction();

        // 1. Lưu vào bảng "orders"
        $stmt = $pdo->prepare("
            INSERT INTO orders (UserID, Status) 
            VALUES (:user_id, :status)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'status' => $status,
        ]);

        // Lấy ID của đơn hàng vừa tạo
        $orderId = $pdo->lastInsertId();

        // 2. Lưu vào bảng "orderdetail"
        $stmtDetail = $pdo->prepare("
            INSERT INTO orderdetail (OrderID, ProductID, Quantity) 
            VALUES (:order_id, :product_id, :quantity)
        ");

        foreach ($cart as $item) {
            $stmtDetail->execute([
                'order_id' => $orderId,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // Xác nhận transaction
        $pdo->commit();

        echo json_encode(['success' => true, 'order_id' => $orderId]);
    } catch (PDOException $e) {
        // Rollback nếu có lỗi
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
