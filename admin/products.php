<?php
require_once '../db.php';

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $productName = $_POST['ProductName'];
    $categoryID = $_POST['CategoryID'];
    $price = $_POST['Price'];
    $stock = $_POST['Stock'];
    $imageURLs = $_POST['ImageURLs'];
    $description = $_POST['Description'];

    $query = "INSERT INTO products (ProductName, CategoryID, Price, Stock, ImageURLs, Description) 
              VALUES (:ProductName, :CategoryID, :Price, :Stock, :ImageURLs, :Description)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':ProductName' => $productName,
        ':CategoryID' => $categoryID,
        ':Price' => $price,
        ':Stock' => $stock,
        ':ImageURLs' => $imageURLs,
        ':Description' => $description,
    ]);

    // Thay thế redirect bằng response JSON
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        echo json_encode(['success' => true]);
        exit;
    }
    header("Location: index.html#products.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $productID = $_POST['ProductID'];
    $productName = $_POST['ProductName'];
    $categoryID = $_POST['CategoryID'];
    $price = $_POST['Price'];
    $stock = $_POST['Stock'];
    $imageURLs = $_POST['ImageURLs'];
    $description = $_POST['Description'];

    $query = "UPDATE products SET ProductName = :ProductName, CategoryID = :CategoryID, Price = :Price, Stock = :Stock, ImageURLs = :ImageURLs, Description = :Description WHERE ProductID = :ProductID";
    $stmt = $pdo->prepare($query); 
    $stmt->execute([
        ':ProductName' => $productName,
        ':CategoryID' => $categoryID,
        ':Price' => $price,
        ':Stock' => $stock,
        ':ImageURLs' => $imageURLs,
        ':Description' => $description,
        ':ProductID' => $productID,
    ]);
    
    // Thêm kiểm tra AJAX request và trả về JSON response
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        echo json_encode(['success' => true]);
        exit;
    }
    header("Location: index.html#products.php");
    exit;
}

// Xử lý xoá sản phẩm
if (isset($_GET['delete_id'])) {
    $productID = $_GET['delete_id'];
    $query = "DELETE FROM products WHERE ProductID = :ProductID";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':ProductID' => $productID]);

    // Thêm kiểm tra AJAX request và trả về JSON response
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        echo json_encode(['success' => true]);
        exit;
    }
    header("Location: index.html");
    exit;
}

// Lấy danh sách sản phẩm
$query = "SELECT * FROM products";
$stmt = $pdo->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Products Page</h2>

<!-- Nút thêm sản phẩm -->
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Product</button>
</div>

<!-- Bảng hiển thị sản phẩm -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= htmlspecialchars($product['ProductID']) ?></td>
            <td><?= htmlspecialchars($product['ProductName']) ?></td>
            <td><?= htmlspecialchars($product['CategoryID']) ?></td>
            <td><?= htmlspecialchars($product['Price']) ?></td>
            <td><?= htmlspecialchars($product['Stock']) ?></td>
            <td><img src="<?= htmlspecialchars($product['ImageURLs']) ?>" alt="Image" style="width:50px;height:50px;"></td>
            <td><?= htmlspecialchars($product['Description']) ?></td>
            <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal<?= $product['ProductID'] ?>">Edit</button>
                <a href="products.php?delete_id=<?= $product['ProductID'] ?>" class="btn btn-danger btn-sm ajax-delete">Delete</a>
            </td>
        </tr>

        <!-- Modal chỉnh sửa sản phẩm -->
        <div class="modal fade" id="editProductModal<?= $product['ProductID'] ?>" tabindex="-1" aria-labelledby="editProductModalLabel<?= $product['ProductID'] ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel<?= $product['ProductID'] ?>">Chỉnh sửa sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="ajax-form" method="POST" action="products.php">
                            <input type="hidden" name="update_product" value="1">
                            <input type="hidden" name="ProductID" value="<?= $product['ProductID'] ?>">
                            <div class="mb-3">
                                <label for="ProductName<?= $product['ProductID'] ?>" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="ProductName<?= $product['ProductID'] ?>" name="ProductName" value="<?= htmlspecialchars($product['ProductName']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="CategoryID<?= $product['ProductID'] ?>" class="form-label">Category</label>
                                <input type="number" class="form-control" id="CategoryID<?= $product['ProductID'] ?>" name="CategoryID" value="<?= htmlspecialchars($product['CategoryID']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="Price<?= $product['ProductID'] ?>" class="form-label">Price</label>
                                <input type="number" step="0.01" class="form-control" id="Price<?= $product['ProductID'] ?>" name="Price" value="<?= htmlspecialchars($product['Price']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="Stock<?= $product['ProductID'] ?>" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="Stock<?= $product['ProductID'] ?>" name="Stock" value="<?= htmlspecialchars($product['Stock']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="ImageURLs<?= $product['ProductID'] ?>" class="form-label">Image URL</label>
                                <input type="text" class="form-control" id="ImageURLs<?= $product['ProductID'] ?>" name="ImageURLs" value="<?= htmlspecialchars($product['ImageURLs']) ?>">
                            </div>
                            <div class="mb-3">
                                <label for="Description<?= $product['ProductID'] ?>" class="form-label">Description</label>
                                <textarea class="form-control" id="Description<?= $product['ProductID'] ?>" name="Description" rows="3"><?= htmlspecialchars($product['Description']) ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal thêm sản phẩm -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">ADD PRODUCT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="ajax-form" method="POST" action="products.php">
                    <input type="hidden" name="add_product" value="1">
                    <div class="mb-3">
                        <label for="ProductName" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="ProductName" name="ProductName" required>
                    </div>
                    <div class="mb-3">
                        <label for="CategoryID" class="form-label">Category</label>
                        <input type="number" class="form-control" id="CategoryID" name="CategoryID" required>
                    </div>
                    <div class="mb-3">
                        <label for="Price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="Price" name="Price" required>
                    </div>
                    <div class="mb-3">
                        <label for="Stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="Stock" name="Stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="ImageURLs" class="form-label">Image URL</label>
                        <input type="text" class="form-control" id="ImageURLs" name="ImageURLs">
                    </div>
                    <div class="mb-3">
                        <label for="Description" class="form-label">Description</label>
                        <textarea class="form-control" id="Description" name="Description" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý form thêm và sửa sản phẩm
    document.querySelectorAll('.ajax-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('products.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Đóng modal
                    const modal = this.closest('.modal');
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                    
                    // Reload trang để cập nhật dữ liệu
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Xử lý xóa sản phẩm
    document.querySelectorAll('.ajax-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this product?')) {
                const deleteUrl = this.getAttribute('href');
                
                fetch(deleteUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Xóa hàng chứa sản phẩm khỏi bảng
                        this.closest('tr').remove();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});
</script>

