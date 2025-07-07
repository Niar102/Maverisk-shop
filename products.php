<?php
session_start();
require_once 'db.php'; // Kết nối database

// Kiểm tra đăng nhập và quyền truy cập
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = false;

if ($isLoggedIn) {
    $user = $_SESSION['user'];
    $userId = $user['UserID'];
    
    // Kiểm tra RoleID
    $stmt = $pdo->prepare("SELECT RoleID FROM users WHERE UserID = ?");
    $stmt->execute([$userId]);
    $userRole = $stmt->fetch();
    
    if ($userRole && ($userRole['RoleID'] == 1 || $userRole['RoleID'] == 2)) {
        $isAdmin = true;
    }
}

// Logout logic
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();

    // Lấy URL của trang hiện tại
    $currentUrl = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

    // Loại bỏ tham số ?logout=true khỏi URL hiện tại
    $redirectUrl = strtok($currentUrl, '?');

    // Chuyển hướng về trang hiện tại (không chứa ?logout=true)
    header("Location: $redirectUrl");
    exit;
}
// Lấy CategoryID từ URL, mặc định là 1 nếu không có tham số
$categoryID = isset($_GET['categoryID']) ? (int)$_GET['categoryID'] : 1;

// Lấy dữ liệu từ bảng `products` với CategoryID được truyền
$query = "SELECT * FROM products WHERE CategoryID = :categoryID";
$stmt = $pdo->prepare($query);
$stmt->execute(['categoryID' => $categoryID]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm số sản phẩm thuộc CategoryID
$productCount = count($products);

try {

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $productsPerPage = 8;
    $offset = ($page - 1) * $productsPerPage;

    // Lấy giá trị lọc
    $sortOrder = isset($_GET['sort']) && $_GET['sort'] === 'desc' ? 'DESC' : 'ASC';
    $minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 1000000;

    // Sửa lại truy vấn để sử dụng $categoryID thay vì hardcode 1
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE CategoryID = :categoryID 
        AND Price BETWEEN :minPrice AND :maxPrice
        ORDER BY Price $sortOrder
        LIMIT :offset, :limit
    ");
    $stmt->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
    $stmt->bindValue(':minPrice', $minPrice, PDO::PARAM_STR);
    $stmt->bindValue(':maxPrice', $maxPrice, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sửa lại truy vấn đếm tổng số sản phẩm
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM products 
        WHERE CategoryID = :categoryID 
        AND Price BETWEEN :minPrice AND :maxPrice
    ");
    $stmt->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
    $stmt->bindValue(':minPrice', $minPrice, PDO::PARAM_STR);
    $stmt->bindValue(':maxPrice', $maxPrice, PDO::PARAM_STR);
    $stmt->execute();
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalProducts / $productsPerPage);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Products - Maverick Dresses</title>
        <link rel="stylesheet" href="styles.css">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
      <!-- Header -->
      <header class="header">
        <div class="logo">
          <a href="homepage.php">
            <img src="logo.png" alt="Maverick Dresses Logo">
          </a>
        </div>
        <div class="search-container">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Enter product name..." />
            <button onclick="searchProducts()">Search</button>
            <div id="searchResults"></div>
        </div>
    </div>
        <div class="user-actions">
            <div>
                <?php if ($isLoggedIn): ?>
                    <?php if ($isAdmin): ?>
                        <a href="admin/index.html" class="admin-link">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="?logout=true" class="logout-link">Logout</a>
                <?php else: ?>
                    <span class="login-text" data-toggle="modal" data-target="#loginModal">Login</span>
                <?php endif; ?>
            </div>
            <span class="cart-text" id="cart-toggle">Cart <i class="fas fa-shopping-cart"></i></span>
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
    <!-- Modal đăng nhập (Login) -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="loginUsername">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="#" data-toggle="modal" data-target="#signUpModal" data-dismiss="modal">Sign up</a></p>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- Modal đăng ký (Sign Up) -->
  <div class="modal fade" id="signUpModal" tabindex="-1" aria-labelledby="signUpModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="signUpModalLabel">Sign Up</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="register.php" method="POST">
                    <div class="form-group">
                        <label for="signUpUsername">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="signUpEmail">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="signUpPassword">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="signUpPhone">Phone Number</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
                </form>
            </div>
        </div>
    </div>
  </div>
      <div class="container">
        <!-- Sort Sidebar -->
        <div class="sort-sidebar">        
            <h3>Price Range</h3>
            <form method="GET" action="products.php">
                <div>
                    <label>Min <input type="number" name="min_price" class="form-control" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>"></label>
                </div>
                <div>
                    <label>Max <input type="number" name="max_price" class="form-control" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>"></label>
                </div>
                <h3>Sort By</h3>
                <label>
                    <input type="radio" name="sort" value="asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'asc') ? 'checked' : ''; ?>>
                    ↑ Price: Low to High
                </label><br>
                <label>
                    <input type="radio" name="sort" value="desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'desc') ? 'checked' : ''; ?>>
                    ↓ Price: High to Low
                </label><br>

                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <button type="button" class="btn btn-secondary" id="clear-filter">Clear Filter</button>
                </div>
            </form>
        </div>

        <!-- Main Content -->
        <div class="content">
          <h2>Category ID <?= $categoryID ?> - Total Items: <?= $productCount ?></h2>
          <div class="product-list">
            <?php if ($productCount > 0): ?>
              <?php foreach ($products as $product): ?>
                <div class="product-item">
                  <div class="card">
                      <a href="./information_category/information.php?productID=<?= htmlspecialchars($product['ProductID']) ?>"> 
                          <img src="<?= htmlspecialchars($product['ImageURLs']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['ProductName']) ?>">
                      </a>
                      <div class="card-body">
                      <h5 class="card-title"><?= htmlspecialchars($product['ProductName']) ?></h5>
                      <p class="card-text">Price: $<?= htmlspecialchars($product['Price']) ?></p>
                      <p class="card-text">Stock: <?= htmlspecialchars($product['Stock']) ?> items</p>
                      <p class="card-text"><?= htmlspecialchars($product['Description']) ?></p>
                    </div>
                  </div>
                  <button class="btn btn-primary add-to-cart-btn" 
                        data-product-id="<?= $product['ProductID']; ?>" 
                        data-product-name="<?= htmlspecialchars($product['ProductName']); ?>" 
                        data-product-price="<?= htmlspecialchars($product['Price']); ?>">
                    Add to Cart
                </button>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p>No products found in this category.</p>
            <?php endif; ?>
          </div>
          <div class="pagination">
                  <?php if ($totalPages > 1): ?>
                      <?php if ($page > 1): ?>
                          <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">&laquo; Previous</a>
                      <?php endif; ?>
                      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                          <?php if ($i == $page): ?>
                              <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="current"><?php echo $i; ?></a>
                          <?php else: ?>
                              <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                          <?php endif; ?>
                      <?php endfor; ?>
                      <?php if ($page < $totalPages): ?>
                          <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next &raquo;</a>
                      <?php endif; ?>
                  <?php endif; ?>
              </div>
        </div>
            <div id="cart-sidebar" class="cart-sidebar">
                <h3>Your Cart</h3>
                <div id="cart-items"></div>
                <button id="checkout-btn" class="btn btn-success">Checkout</button>
            </div>
        </div>

      <!-- Footer -->
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
      <script>
        const cartSidebar = document.getElementById('cart-sidebar');
        const cartToggle = document.getElementById('cart-toggle');
        const cartItems = document.getElementById('cart-items');
        const checkoutButton = document.getElementById('checkout-btn');

        // Toggle cart sidebar visibility
        cartToggle.addEventListener('click', () => {
            cartSidebar.classList.toggle('active');
        });

        // Sửa lại phần khởi tạo giỏ hàng
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Sửa lại hàm saveCartToLocalStorage
        function saveCartToLocalStorage() {
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        // Sửa lại phần xử lý nút Add to Cart
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.productId;
                const productName = button.dataset.productName;
                const productPrice = parseFloat(button.dataset.productPrice);

                // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
                const existingProductIndex = cart.findIndex(item => item.id === productId);

                if (existingProductIndex !== -1) {
                    // Nếu sản phẩm đã tồn tại, tăng số lượng
                    cart[existingProductIndex].quantity += 1;
                } else {
                    // Nếu sản phẩm chưa tồn tại, thêm mới
                    cart.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: 1
                    });
                }

                saveCartToLocalStorage();
                renderCart();
                
                // Thông báo cho người dùng
                alert('Product added to cart successfully!');
            });
        });

        // Sửa lại hàm renderCart để hiển thị chi tiết hơn
        function renderCart() {
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';

            if (cart.length === 0) {
                cartItems.innerHTML = '<p>Your cart is empty.</p>';
                return;
            }

            cart.forEach((item, index) => {
                const itemTotal = (item.price * item.quantity).toFixed(2);
                cartItems.innerHTML += `
                    <div class="cart-item">
                        <div class="cart-item-details">
                            <h6>${item.name}</h6>
                            <p>Price: $${item.price} x ${item.quantity}</p>
                            <p>Total: $${itemTotal}</p>
                        </div>
                        <div class="cart-item-actions">
                            <button class="btn btn-sm btn-primary quantity-btn" data-index="${index}" data-action="decrease">-</button>
                            <span class="quantity-display">${item.quantity}</span>
                            <button class="btn btn-sm btn-primary quantity-btn" data-index="${index}" data-action="increase">+</button>
                            <button class="btn btn-sm btn-danger remove-cart-item" data-index="${index}">Remove</button>
                        </div>
                    </div>
                `;
            });

            // Hiển thị tổng giá trị giỏ hàng
            const totalAmount = calculateTotalPrice();
            cartItems.innerHTML += `
                <div class="cart-total">
                    <h5>Total: $${totalAmount}</h5>
                </div>
            `;

            // Thêm xử lý sự kiện cho các nút
            addCartItemEventListeners();
        }

        // Thêm hàm xử lý sự kiện cho các nút trong giỏ hàng
        function addCartItemEventListeners() {
            // Xử lý nút tăng/giảm số lượng
            document.querySelectorAll('.quantity-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const index = parseInt(e.target.dataset.index);
                    const action = e.target.dataset.action;

                    if (action === 'increase') {
                        cart[index].quantity += 1;
                    } else if (action === 'decrease') {
                        if (cart[index].quantity > 1) {
                            cart[index].quantity -= 1;
                        } else {
                            cart.splice(index, 1);
                        }
                    }

                    saveCartToLocalStorage();
                    renderCart();
                });
            });

            // Xử lý nút xóa sản phẩm
            document.querySelectorAll('.remove-cart-item').forEach(button => {
                button.addEventListener('click', (e) => {
                    const index = parseInt(e.target.dataset.index);
                    cart.splice(index, 1);
                    saveCartToLocalStorage();
                    renderCart();
                });
            });
        }

        // Cập nhật xử lý checkout
        checkoutButton.addEventListener('click', () => {
            if (cart.length === 0) {
                alert("Your cart is empty!");
            } else {
                const totalAmount = calculateTotalPrice();
                localStorage.setItem('cart-items', JSON.stringify(cart));
                localStorage.setItem('totalAmount', totalAmount);
                window.location.href = 'checkout.php';
            }
        });

        // Hàm tính tổng số tiền
        function calculateTotalPrice() {
            return cart.reduce((total, item) => total + item.price * item.quantity, 0).toFixed(2);
        }

        // Gọi hàm render giỏ hàng khi trang tải
        document.addEventListener('DOMContentLoaded', renderCart);

        document.getElementById('clear-filter').addEventListener('click', () => {
            // Chuyển về URL mặc định mà không có tham số GET
            window.location.href = 'products.php';
        });
        function searchProducts() {

// lấy giá trị tìm kiếm nhập vào
const searchInput = document.getElementById('searchInput');

// kết quả tìm kiếm
const searchResults = document.getElementById('searchResults');

// giá trị tìm kiếm
const query = searchInput.value.trim();


// nếu giá trị tìm kiếm khác rỗng
if (query.length > 0) {
    // gửi giá trị tìm kiếm đến backend
    fetch(`search.php?query=${encodeURIComponent(query)}`)

    // nếu có kết quả
        .then(response => response.text())
        // hiển thị kết quả
        .then(data => {
            searchResults.innerHTML = data;
            searchResults.style.display = 'block';
        })
        .catch(error => console.error('Error:', error));
} else {
    searchResults.style.display = 'none';
}
}
    </script>
    <style>
        .logout-link {
            color: red;
        }
        .cart-sidebar {
            width: 300px;
            background-color: #f9f9f9;
            position: fixed;
            right: -300px;
            top: 100px;
            height: 100vh;
            overflow-y: auto;
            transition: right 0.3s ease-in-out;
            padding: 20px;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .cart-sidebar.active {
            right: 0;
            top: 100px;
        }

        .cart-text, .login-text {
            cursor: pointer;
            color: #007bff;
        }

        .cart-text:hover, .login-text:hover {
            text-decoration: underline;
        }

        .product-item {
            margin-bottom: 20px;
        }

        .sort-sidebar {
            width: 200px;
            margin-right: 20px;
        }

        .content {
            flex: 1;
        }
        #searchResults {
          position: absolute;
          top: 100%;
          left: 0;
          right: 0;
          background: white;
          border: 1px solid #ddd;
          border-radius: 4px;
          max-height: 400px;
          overflow-y: auto;
          display: none;
          z-index: 1000;
      }

      .product-card {
          display: flex;
          align-items: center;
          padding: 10px;
          border-bottom: 1px solid #eee;
      }

      .product-card img {
          width: 50px;
          height: 50px;
          object-fit: cover;
          margin-right: 10px;
      }

      .product-card h3 {
          margin: 0;
          font-size: 14px;
      }

      .product-card .price {
          margin: 5px 0;
          color: #e44d26;
          font-weight: bold;
      }

      .product-card .view-details {
          padding: 5px 10px;
          background: #A1D6E2;
          color: white;
          text-decoration: none;
          border-radius: 4px;
          margin-left: auto;
      }

      .product-card:hover {
          background: #f5f5f5;
      }

      .cart-item {
          border-bottom: 1px solid #ddd;
          padding: 10px 0;
          margin-bottom: 10px;
      }

      .cart-item-details {
          margin-bottom: 10px;
      }

      .cart-item-details h6 {
          margin: 0;
          color: #333;
      }

      .cart-item-details p {
          margin: 5px 0;
          font-size: 0.9em;
          color: #666;
      }

      .cart-item-actions {
          display: flex;
          align-items: center;
          gap: 8px;
      }

      .quantity-display {
          padding: 0 10px;
          font-weight: bold;
      }

      .cart-total {
          margin-top: 20px;
          padding-top: 10px;
          border-top: 2px solid #ddd;
      }

      .quantity-btn {
          width: 30px;
          height: 30px;
          padding: 0;
          display: flex;
          align-items: center;
          justify-content: center;
      }
    </style>
    <script src="scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
