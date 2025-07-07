<?php
    session_start();
    require_once '../db.php'; // Kết nối database
    
    // Kiểm tra nếu người dùng đã đăng nhập
    $isLoggedIn = isset($_SESSION['user']);

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
    // Lấy ProductID từ tham số URL
    $productID = isset($_GET['productID']) ? (int)$_GET['productID'] : 0;

    // Kiểm tra nếu ProductID hợp lệ
    if ($productID <= 0) {
        die("Sản phẩm không tồn tại!");
    }

    // Truy vấn chi tiết sản phẩm
    $query = "SELECT * FROM products WHERE ProductID = :productID";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':productID' => $productID]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra nếu không tìm thấy sản phẩm
    if (!$product) {
        die("Sản phẩm không tồn tại!");
    }
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Products - Maverick Dresses</title>
        <link rel="stylesheet" href="../styles.css">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
      <!-- Header -->
      <header class="header">
        <div class="logo">
          <a href="../homepage.php">
            <img src="../logo.png" alt="Maverick Dresses Logo">
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
                    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 1): ?>
                        <a href="../admin/index.html" class="admin-link">Admin Dashboard</a>
                    <?php endif; ?>
                    <a href="?logout=true" class="logout-link">Logout</a>
                <?php else: ?>
                    <span class="login-text" data-toggle="modal" data-target="#loginModal">Login</span>
                <?php endif; ?>
            </div>
            <span class="cart-text" id="cart-toggle">Cart <i class="fas fa-shopping-cart"></i></span>
        </div>
        <nav class="navbar">
            <a href="../products.php?categoryID=1">Shirts</a>
            <a href="../products.php?categoryID=2">Skirts</a>
            <a href="../products.php?categoryID=3">Frocks</a>
            <a href="../products.php?categoryID=4">P.T. T-shirts</a>
            <a href="../products.php?categoryID=5">P.T. shorts</a>
            <a href="../products.php?categoryID=6">P.T. track pants</a>
            <a href="../products.php?categoryID=7">Belts</a>
            <a href="../products.php?categoryID=8">Ties</a>
            <a href="../products.php?categoryID=9">Logos</a>
            <a href="../products.php?categoryID=10">Socks</a>
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
        <div class="details-section">
            <div class="details-image">
                <img src="<?= htmlspecialchars($product['ImageURLs']) ?>" 
                     alt="<?= htmlspecialchars($product['ProductName']) ?>" 
                     style="width:300px;">
            </div>

            <div class="details-info">
                <h2><?= htmlspecialchars($product['ProductName']) ?></h2>
                <div class="rating">
                    <span>Rating:</span>
                    <span class="stars">★★★★★</span>
                    <span class="score">5.0</span>
                </div>

                <div class="size">
                    <span>Size:</span>
                    <button class="size-btn">S</button>
                    <button class="size-btn">M</button>
                    <button class="size-btn">L</button>
                </div>

                <div class="price">
                    <span>Price: $<?= htmlspecialchars($product['Price']) ?></span>
                </div>

                <div class="actions">
                    <button class="btn btn-primary add-to-cart-btn" 
                            data-product-id="<?= $product['ProductID']; ?>" 
                            data-product-name="<?= htmlspecialchars($product['ProductName']); ?>" 
                            data-product-price="<?= htmlspecialchars($product['Price']); ?>">
                        Add to Cart
                    </button>
                    <a href="../feedback/feedback.php?productID=<?= htmlspecialchars($product['ProductID']) ?>" 
                       class="feedback-link">
                        <button class="btn btn-primary">Feedback</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
      <footer>
        <div class="footer-container">
            <div class="footer-column">
                <h3>Products</h3>
                <ul>
                    <li><a href="products.php?categoryID=1">Shirts</a></li>
                    <li><a href="products.php?categoryID=2">Skirts</a></li>
                    <li><a href="products.php?categoryID=3">Frocks</a></li>
                    <li><a href="products.php?categoryID=4">P.T. T-shirts</a></li>
                    <li><a href="products.php?categoryID=5">P.T. shorts</a></li>
                    <li><a href="products.php?categoryID=6">P.T. track pants</a></li>
                    <li><a href="products.php?categoryID=7">Belts</a></li>
                    <li><a href="products.php?categoryID=8">Ties</a></li>
                    <li><a href="products.php?categoryID=9">Logos</a></li>
                    <li><a href="products.php?categoryID=10">Socks</a></li>
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

        // Lấy danh sách sản phẩm trong giỏ hàng từ localStorage
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Hàm tính tổng số tiền
function calculateTotalPrice() {
    return cart.reduce((total, item) => total + item.price * item.quantity, 0).toFixed(2);
}

// Hàm render giỏ hàng
function renderCart() {
    const cartItems = document.getElementById('cart-items');
    cartItems.innerHTML = '';

    if (cart.length === 0) {
        cartItems.innerHTML = '<p>Your cart is empty.</p>';
    } else {
        cart.forEach((item, index) => {
            cartItems.innerHTML += `
                <div class="cart-item">
                    <p>${item.name} (${item.quantity}) - $${item.price.toFixed(2)}</p>
                    <button class="btn btn-danger btn-sm remove-cart-item" data-index="${index}">Remove</button>
                </div>
            `;
        });

        // Thêm phần tổng số tiền vào giỏ hàng
        cartItems.innerHTML += `
            <div class="cart-total mt-3">
                <h5>Total: $${calculateTotalPrice()}</h5>
            </div>
        `;

        // Gắn sự kiện xóa sản phẩm
        document.querySelectorAll('.remove-cart-item').forEach(button => {
            button.addEventListener('click', (event) => {
                const index = event.target.dataset.index;
                cart.splice(index, 1);

                // Cập nhật localStorage và render lại
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            });
        });
    }
}

// Gọi hàm render giỏ hàng khi trang tải
document.addEventListener('DOMContentLoaded', renderCart);

// Xử lý sự kiện khi nhấn "Add to Cart"
document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', () => {
        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        const productPrice = parseFloat(button.dataset.productPrice);

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        const existingProduct = cart.find(item => item.id === productId);

        if (existingProduct) {
            existingProduct.quantity += 1; // Tăng số lượng nếu sản phẩm đã tồn tại
        } else {
            cart.push({ id: productId, name: productName, price: productPrice, quantity: 1 });
        }

        // Lưu lại giỏ hàng vào localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart(); // Render lại giỏ hàng
    });
});


// Gọi hàm render giỏ hàng khi trang tải
document.addEventListener('DOMContentLoaded', renderCart);


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
        .details-section {
    display: flex;
    gap: 30px;
    padding: 30px;
    margin: 50px auto;
    max-width: 1200px;
    background-color: var(--white);
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.details-image {
    flex: 0 0 400px; /* Cố định kích thước, không co giãn */
    border-radius: 8px;
    overflow: hidden; /* Ngăn ảnh tràn ra ngoài */
    background-color: var(--gray-light);
}

.details-image img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* Giữ tỷ lệ ảnh, không bị méo */
    max-height: 500px; /* Giới hạn chiều cao tối đa */
    display: block; /* Loại bỏ khoảng trống dưới ảnh */
}
  
  .details-info {
    flex: 2;
  }
  
  .details-info h2 {
    font-size: 24px;
    margin-bottom: 10px;
    color: #333;
  }
  
  .rating {
    margin: 10px 0;
  }
  
  .rating .stars {
    color: gold;
    font-size: 18px;
  }
  
  .rating .score {
    margin-left: 10px;
    font-weight: bold;
    font-size: 16px;
  }
  
  .size {
    margin: 10px 0;
  }
  
  .size span {
    margin-right: 10px;
    font-weight: bold;
  }
  
  .size .size-btn {
    margin: 0 5px;
    padding: 5px 10px;
    border: 1px solid #ccc;
    cursor: pointer;
    background-color: white;
    transition: background-color 0.3s ease;
  }
  
  .size .size-btn:hover {
    background-color: #f7bdbd;
  }
  
  .size .size-btn.selected {
    background-color: #ff7f7f;
    color: white;
  }
  
  .price {
    font-size: 18px;
    margin: 10px 0;
    font-weight: bold;
  }
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
        .container {
            margin-top: 140px;
            justify-content: center;
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
      .details-section {
        display: flex;
        justify-content: center;
        align-items: center;
      }
    </style>
    <script src="../scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
