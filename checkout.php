<?php
session_start();

require_once 'db.php';

// Kiểm tra đăng nhập và quyền truy cập
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = false;

if (!$isLoggedIn) {
    echo "Người dùng chưa đăng nhập";
    exit;
} else {
    $user = $_SESSION['user'];
    $userId = $user['UserID'];
    $username = $user['Username'];
    
    // Kiểm tra RoleID
    $stmt = $pdo->prepare("SELECT RoleID FROM users WHERE UserID = ?");
    $stmt->execute([$userId]);
    $userRole = $stmt->fetch();
    
    if ($userRole && ($userRole['RoleID'] == 1 || $userRole['RoleID'] == 2)) {
        $isAdmin = true;
    }
}

// Handle POST request for order creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_order') {
    $userId = $user['UserID'];
    $address = $_POST['address'];
    $orderDetails = json_decode($_POST['cart-items'], true);  // Thay đổi từ 'cart' thành 'cart-items'
    $total = $_POST['totalAmount'];

    try {
        // Bắt đầu giao dịch
        $pdo->beginTransaction();

        // Thêm chi tiết đơn hàng vào bảng orderdetail trước
        $orderDetailsIds = [];
        foreach ($orderDetails as $item) {
            $stmt = $pdo->prepare("INSERT INTO orderdetail (ProductID, Quantity) VALUES (:productId, :quantity)");
            $stmt->execute([
                'productId' => $item['id'],
                'quantity' => $item['quantity']
            ]);
            $orderDetailsIds[] = $pdo->lastInsertId(); // Sửa lại để lưu vào mảng
        }

        // Thêm đơn hàng vào bảng orders
        $stmt = $pdo->prepare("INSERT INTO orders (OrderDetailID, UserID, Status, Address, Total) VALUES (:orderDetailId, :userId, :status, :address, :total)");
        $stmt->execute([
            'orderDetailId' => implode(',', $orderDetailsIds), // Chuyển mảng thành chuỗi
            'userId' => $userId,
            'status' => 'Pending',
            'address' => $address,
            'total' => $total
        ]);
        
        $orderId = $pdo->lastInsertId();
        $pdo->commit();
        
        echo json_encode(['success' => true, 'orderId' => $orderId]); // Trả về JSON
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Fetch user information including address
$userInfo = [];

if (isset($userId)) {
    $stmt = $pdo->prepare("SELECT Username as name, Email as email, Phone as phone, Address as address FROM users WHERE UserID = :id");
    $stmt->execute(['id' => $userId]);
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .checkout-container {
            width: 80%;
            max-width: 800px;
            margin: 150px auto 20px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .checkout-container h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            border-bottom: 2px solid #A1D6E2;
            padding-bottom: 10px;
        }

        .info-container {
            margin: 25px 0;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #A1D6E2;
            transition: all 0.3s ease;
        }

        .info-row:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-weight: bold;
            width: 120px;
            color: #333;
        }

        .info-value {
            flex-grow: 1;
            margin: 0 15px;
            color: #555;
        }

        .edit-btn {
            padding: 8px 20px;
            background-color: #A1D6E2;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .edit-btn:hover {
            background-color: #7fcecb;
            transform: scale(1.05);
        }

        .edit-input {
            padding: 10px;
            border: 2px solid #A1D6E2;
            border-radius: 5px;
            width: 100%;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .edit-input:focus {
            outline: none;
            border-color: #7fcecb;
            box-shadow: 0 0 5px rgba(161, 214, 226, 0.3);
        }

        .save-btn {
            padding: 8px 20px;
            background-color: #A1D6E2;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            background-color: #7fcecb;
            transform: scale(1.05);
        }

        .payment-method {
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .payment-method h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #A1D6E2;
            padding-bottom: 10px;
        }

        .cod-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .payment-icon {
            font-size: 24px;
        }

        .payment-details {
            flex-grow: 1;
        }

        .payment-details p {
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        .payment-details small {
            color: #666;
        }

        .form-group {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: right;
        }

        .form-group p {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .submit-btn {
            padding: 12px 30px;
            background-color: #A1D6E2;
            color: #333;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #7fcecb;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="homepage.php">
                <img src="logo.png" alt="Maverick Dresses Logo">
            </a>
        </div>
        <div class="search-container">
            <div class="search-box">
                <input type="text" placeholder="Search">
                <button>Search</button>
            </div>
        </div>
        <div class="user-actions">
            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin/index.html" class="admin-link">Admin Dashboard</a>
                <?php endif; ?>
                <a href="?logout=true">Logout</a>
            <?php else: ?>
                <div>Login</div>
            <?php endif; ?>
            <div>Cart</div>
        </div>
        <nav class="navbar">
        <a href="products.php?categoryID=1">Shirts</a>
        <a href="products.php?categoryID=2">Skirts</a>
        <a href="products.php?categoryID=3">Frocks</a>
        <a href="products.php?categoryID=4">P.T. T-shirts</a>
        <a href="products.php?categoryID=5">P.T. shorts</a>
        <a href="products.php?categoryID=6">P.T. track pants</a>
        <a href="products.php?categoryID=7">Belts</a>
        <a href="products.php?categoryID=8">Ties</a>
        <a href="products.php?categoryID=9">Logos</a>
        <a href="products.php?categoryID=10">Socks</a>
        </nav>
    </header>

    <div class="checkout-container">
        <h1>Confirm Your Order</h1>
        <div class="info-container">
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value"><?php echo htmlspecialchars($userInfo['name'] ?? ''); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($userInfo['email'] ?? ''); ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>  
                <span class="info-value"><?php echo htmlspecialchars($userInfo['phone'] ?? ''); ?></span>
            </div>
            <div class="info-row" id="address-container">
                <span class="info-label">Address:</span>
                <span class="info-value" id="address-display"><?php echo htmlspecialchars($userInfo['address']); ?></span>
            </div>
        </div>

        <div class="payment-method">
            <h3>Payment Method</h3>
            <div class="cod-info">
                <div class="payment-icon">
                    <span>💰</span>
                </div>
                <div class="payment-details">
                    <p>Cash on Delivery (COD)</p>
                    <small>Pay when you receive the package</small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <p>Total amount: <span id="totalAmount"></span></p>
            <button type="button" class="submit-btn" onclick="submitOrder()">Confirm Order</button>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-column">
                <h3>Products</h3>
                <ul>
                    <li><a href="Shirts.php">Shirts</a></li>
                    <li><a href="Skirts.php">Skirts</a></li>
                    <li><a href="Frocks.php">Frocks</a></li>
                    <li><a href="PTTShirts.php">P.T. T-shirts</a></li>
                    <li><a href="PTShorts.php">P.T. shorts</a></li>
                    <li><a href="PTTrackPants.php">P.T. track pants</a></li>
                    <li><a href="Belts.php">Belts</a></li>
                    <li><a href="Ties.php">Ties</a></li>
                    <li><a href="Logos.php">Logos</a></li>
                    <li><a href="Socks.php">Socks</a></li>
                </ul>
            </div>
            <div class="footer-column">
              <h3>About Us</h3>
              <ul>
                  <li>Address: 285 Đội Cấn, Ba Đình, Hà Nội</li>
                  <li>Phone: 0123456789</a></li>
                  <li>Email: Maverick@abc.abc </li>
              </ul>
            </div>
            <div class="footer-column">
                <h3>Location</h3>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d465.49111564303325!2d105.8186661!3d21.0355297!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab0d6e603741%3A0x208a848932ac2109!2sAptech%20Computer%20Education!5e0!3m2!1svi!2s!4v1736082660074!5m2!1svi!2s" 
                            width="400"  
                            height="260" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
            </div>

        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Maverick Dresses</p>
        </div>
    </footer>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    const totalAmount = localStorage.getItem('totalAmount');

    function submitOrder() {
        const address = document.getElementById('address-display').innerText.trim();

        // Kiểm tra giỏ hàng
        const cartItems = JSON.parse(localStorage.getItem('cart-items')) || [];
        if (cartItems.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        // Lấy tổng tiền từ localStorage
        if (!totalAmount || isNaN(totalAmount)) {
            alert('Invalid total amount');
            return;
        }

        // Gửi yêu cầu tạo đơn hàng
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=submit_order&address=${encodeURIComponent(address)}&cart-items=${encodeURIComponent(JSON.stringify(cartItems))}&totalAmount=${encodeURIComponent(totalAmount)}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Xóa giỏ hàng sau khi đặt hàng thành công
                localStorage.removeItem('cart');
                localStorage.removeItem('totalAmount');
                alert('Order successfully created!');
                // Chuyển hướng đến trang xác nhận đơn hàng
                window.location.href = 'checkout.php';
            } else {
                alert('Failed to create order. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the order. Please try again.');
        });
    }

    // Hiển thị tổng tiền
    function displayTotalAmount() {
        if (totalAmount !== null) {
            document.getElementById('totalAmount').innerText = totalAmount;
        } else {
            document.getElementById('totalAmount').innerText = '0'; // Nếu không có totalAmount, hiển thị 0
        }
    }

    // Gọi hàm khi trang đã tải xong
    document.addEventListener('DOMContentLoaded', displayTotalAmount);
</script>
</body>
</html>