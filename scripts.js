// Xử lý thêm sản phẩm vào giỏ hàng
document.addEventListener('click', function (event) {
    if (event.target && event.target.textContent === 'Add to Cart') {
        const productItem = event.target.closest('.product-item');
        const productName = productItem.querySelector('h4').textContent;
        const productPrice = productItem.querySelector('p').textContent.replace('Price: $', '');

        // Gửi dữ liệu sản phẩm tới server
        fetch('cart_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add',
                product: {
                    name: productName,
                    price: parseFloat(productPrice),
                },
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                } else {
                    alert('Error adding product to cart.');
                }
            })
            .catch(error => console.error('Error:', error));
    }
});
function renderCart() {
    fetch('cart_handler.php')
        .then(response => response.json())
        .then(data => {
            const cartContainer = document.querySelector('.sidebar');
            cartContainer.innerHTML = ''; // Xóa nội dung cũ

            if (data.cart.length === 0) {
                cartContainer.innerHTML = '<p>Your cart is empty.</p>';
            } else {
                data.cart.forEach(item => {
                    const cartItem = document.createElement('div');
                    cartItem.classList.add('cart-item');
                    cartItem.innerHTML = `
                        <h4>${item.name}</h4>
                        <p>$${item.price.toFixed(2)}</p>
                        <button data-remove="${item.name}">Remove</button>
                    `;
                    cartContainer.appendChild(cartItem);
                });
            }
        });
}

// Xử lý xóa sản phẩm trong giỏ hàng
document.addEventListener('click', function (event) {
    if (event.target && event.target.textContent === 'Remove') {
        const productName = event.target.getAttribute('data-remove');

        fetch('cart_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'remove',
                product_name: productName,
            }),
        })
            .then(response => response.json())
            .then(() => {
                renderCart();
            });
    }
});

// Gọi renderCart khi sidebar mở
document.getElementById('cart-toggle').addEventListener('click', renderCart);

function changePage(page) {
    loadProducts(page);
}
document.addEventListener('DOMContentLoaded', function () {
    loadProducts(1); // Tải trang đầu tiên
});


function renderProducts(currentPage) {
    productListContainer.innerHTML = ""; // Xóa nội dung cũ

    const start = (currentPage - 1) * productsPerPage;
    const end = start + productsPerPage;
    const currentProducts = products.slice(start, end);

    currentProducts.forEach(product => {
        productListContainer.innerHTML += `
            <div class="product-item">
                <h4>${product}</h4>
                <img src="img/1.jpg" alt="${product} Image">
                <p>Price: $100</p>
                <button>Buy now</button>
                <button>Add to Cart</button>
            </div>
        `;
    });
}
function attachAddToCartEvents() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-button');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.productId;

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ productId: productId }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Added to cart successfully!');
                } else {
                    alert('Failed to add to cart: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
}
function renderPagination(currentPage) {
    paginationContainer.innerHTML = ""; // Xóa nội dung phân trang cũ

    if (totalPages > 1) {
        // Nút Previous
        if (currentPage > 1) {
            const prevButton = `<a href="#" onclick="changePage(${currentPage - 1}); return false;">&laquo; Previous</a>`;
            paginationContainer.innerHTML += prevButton;
        }

        // Số trang
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                paginationContainer.innerHTML += `<a href="#" class="current">${i}</a>`;
            } else {
                paginationContainer.innerHTML += `<a href="#" onclick="changePage(${i}); return false;">${i}</a>`;
            }
        }

        // Nút Next
        if (currentPage < totalPages) {
            const nextButton = `<a href="#" onclick="changePage(${currentPage + 1}); return false;">Next &raquo;</a>`;
            paginationContainer.innerHTML += nextButton;
        }
    } else {
        paginationContainer.innerHTML = `<a href="#" class="current">1</a>`;
    }
}


function changePage(page) {
    renderProducts(page);
    renderPagination(page);
}

// Hiển thị trang đầu tiên
changePage(1);

const cartSidebar = document.getElementById('cart-sidebar');
    const cartToggle = document.getElementById('cart-toggle');
    const cartItems = document.getElementById('cart-items');
    let cart = [];

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

            renderCart();
        });
    });

    // Render cart items
    function renderCart() {
        cartItems.innerHTML = '';
        if (cart.length === 0) {
            cartItems.innerHTML = '<p>Cart is empty...</p>';
        } else {
            cart.forEach(item => {
                cartItems.innerHTML += `
                    <div class="cart-item">
                        <p>${item.name} (${item.quantity}) - ${item.price}</p>
                    </div>
                `;
            });
        }
    }
document.getElementById('clear-filter').addEventListener('click', function () {
    // Chuyển về URL gốc (Shirts.php) không chứa bất kỳ tham số GET nào
    window.location.href = 'Shirts.php';
});

document.getElementById('confirm-order-btn').addEventListener('click', function () {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    if (cart.length === 0) {
        alert('Your cart is empty!');
        return;
    }

    // Xác nhận hành động với người dùng
    if (confirm('Do you want to confirm your order?')) {
        // Gửi thông tin đơn hàng và giỏ hàng lên server
        fetch('process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart=${encodeURIComponent(JSON.stringify(cart))}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order confirmed successfully!');
                localStorage.removeItem('cart'); // Xóa giỏ hàng sau khi đặt hàng thành công
                window.location.href = 'homepage.php'; // Chuyển về trang chủ
            } else {
                alert('Failed to confirm order. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
});

// Search functionality
function searchProducts() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const query = searchInput.value.trim();

    if (query.length > 0) {
        fetch(`search.php?query=${encodeURIComponent(query)}`)
            .then(response => response.text())
            .then(data => {
                searchResults.innerHTML = data;
                searchResults.style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    } else {
        searchResults.style.display = 'none';
    }
}