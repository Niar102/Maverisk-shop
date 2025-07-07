<?php
session_start();
require_once 'connectdb.php';

try {
    $db = new Connection();
    $pdo = $db->getConnection();

    // Lấy giá trị từ URL
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $productsPerPage = 8;
    $offset = ($page - 1) * $productsPerPage;

    // Lấy giá trị lọc
    $sortOrder = isset($_GET['sort']) && $_GET['sort'] === 'desc' ? 'DESC' : 'ASC';
    $minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 1000000;

    // Truy vấn sản phẩm với các bộ lọc
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE CategoryID = 1 
        AND Price BETWEEN :minPrice AND :maxPrice
        ORDER BY Price $sortOrder
        LIMIT :offset, :limit
    ");
    $stmt->bindValue(':minPrice', $minPrice, PDO::PARAM_STR);
    $stmt->bindValue(':maxPrice', $maxPrice, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tổng số sản phẩm để phân trang
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM products 
        WHERE CategoryID = 1 
        AND Price BETWEEN :minPrice AND :maxPrice
    ");
    $stmt->bindValue(':minPrice', $minPrice, PDO::PARAM_STR);
    $stmt->bindValue(':maxPrice', $maxPrice, PDO::PARAM_STR);
    $stmt->execute();
    $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalProducts / $productsPerPage);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} finally {
    $db->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shirts Collection</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <span class="login-text" data-toggle="modal" data-target="#loginModal">Login</span>
        <span class="cart-text" id="cart-toggle">Cart <i class="fas fa-shopping-cart"></i></span>
    </div>
    <nav class="navbar" custom-navbar>
      <a href="Shirts.php">Shirts</a>
      <a href="Skirts.php">Skirts</a>
      <a href="Frocks.php">Frocks</a>
      <a href="PTTShirts.php">P.T. T-shirts</a>
      <a href="PTShorts.php">P.T. shorts</a>
      <a href="PTTrackPants.php">P.T. track pants</a>
      <a href="Belts.php">Belts</a>
      <a href="Ties.php">Ties</a>
      <a href="Logos.php">Logos</a>
      <a href="Socks.php">Socks</a>
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
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="loginUsername">Username</label>
                        <input type="text" class="form-control" id="loginUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <div id="loginError" class="text-danger mt-2" style="display: none;"></div>
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
                  <form method="POST" action="register.php">
                      <div class="form-group">
                          <label for="signUpUsername">Username</label>
                          <input type="text" class="form-control" id="signUpUsername" name="username" pattern="^[A-Za-z0-9]+$" required>
                          <small class="form-text text-muted">Username chỉ chứa chữ cái và số.</small>
                      </div>
                      <div class="form-group">
                          <label for="signUpEmail">Email</label>
                          <input type="email" class="form-control" id="signUpEmail" name="email" required>
                      </div>
                      <div class="form-group">
                          <label for="signUpPassword">Password</label>
                          <input type="password" class="form-control" id="signUpPassword" name="password" minlength="6" required>
                          <small class="form-text text-muted">Mật khẩu phải có ít nhất 6 ký tự.</small>
                      </div>
                      <div class="form-group">
                          <label for="confirmPassword">Confirm Password</label>
                          <input type="password" class="form-control" id="confirmPassword" minlength="6" required>
                          <small class="form-text text-muted">Confirm Password</small>
                      </div>
                      <div class="form-group">
                          <label for="signUpPhone">Phone Number</label>
                          <input type="text" class="form-control" id="signUpPhone" name="phone" pattern="^[0-9]+$" required>
                          <small class="form-text text-muted">Số điện thoại chỉ chứa số.</small>
                      </div>
                      <div class="form-group">
                          <label for="address">Address</label>
                          <input type="text" class="form-control" id="address" name="address" required>
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
        <form method="GET" action="Shirts.php">
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

    <!-- Product List -->
    <div class="content">
        <h1>Our Shirts Collection</h1>
        <div class="product-list">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <h4><?php echo htmlspecialchars($product['ProductName']); ?></h4>
                        <img src="<?php echo htmlspecialchars($product['ImageURLs']); ?>" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                        <p>Price: $<?php echo number_format($product['Price'], 2); ?></p>
                        <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product['ProductID']; ?>">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found matching your criteria.</p>
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


    <!-- Cart Sidebar -->
    <div id="cart-sidebar" class="cart-sidebar">
        <h3>Your Cart</h3>
            <div id="cart-items">
                <p>Cart is empty...</p>
            </div>
        <button class="btn btn-success mt-3" id="checkout-btn" href="checkout.php">Checkout</button>
    </div>
</div>

<footer>
    <div class="footer-container">
        <p>&copy; 2024 Maverick Dresses</p>
    </div>
</footer>

<script>
    const cartSidebar = document.getElementById('cart-sidebar');
    const cartToggle = document.getElementById('cart-toggle');
    const cartItems = document.getElementById('cart-items');
    const checkoutButton = document.getElementById('checkout-btn');
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Toggle cart sidebar visibility
    cartToggle.addEventListener('click', () => {
        cartSidebar.classList.toggle('active');
    });

    // Add to cart
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.productId;
            const productName = button.closest('.product-item').querySelector('h4').innerText;
            const productPrice = button.closest('.product-item').querySelector('p').innerText;

            // Check if product already in cart
            const existingProduct = cart.find(item => item.id === productId);
            if (existingProduct) {
                existingProduct.quantity += 1;
            } else {
                cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
            }

            // Save cart to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            renderCart();
        });
    });

    // Render cart items
    function renderCart() {
        cartItems.innerHTML = '';
        if (cart.length === 0) {
            cartItems.innerHTML = '<p>Cart is empty...</p>';
        } else {
            cart.forEach((item, index) => {
                cartItems.innerHTML += `
                    <div class="cart-item">
                        <p>${item.name} (${item.quantity}) - ${item.price}</p>
                        <button class="btn btn-danger btn-sm remove-cart-item" data-index="${index}">Remove</button>
                    </div>
                `;
            });

            // Add event listeners for remove buttons
            document.querySelectorAll('.remove-cart-item').forEach(button => {
                button.addEventListener('click', (event) => {
                    const index = event.target.dataset.index;
                    cart.splice(index, 1);

                    // Save cart to localStorage
                    localStorage.setItem('cart', JSON.stringify(cart));

                    renderCart();
                });
            });
        }
    }

    // Clear cart
    checkoutButton.addEventListener('click', () => {
        if (cart.length === 0) {
            alert("Your cart is empty!");
        } else {
            // Clear cart
            cart = [];
            localStorage.removeItem('cart');
            renderCart();

            alert("Thank you for your purchase!");
        }
    });

    // Initial render
    renderCart();
    
    document.getElementById('clear-filter').addEventListener('click', () => {
        // Chuyển về URL mặc định mà không có tham số GET
        window.location.href = 'Shirts.php';
    });
</script>

<style>
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

    .container {
        display: flex;
        gap: 20px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>