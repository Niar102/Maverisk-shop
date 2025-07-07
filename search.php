<?php
$host = 'localhost';
$dbname = 'maverick';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (isset($_GET['query'])) {
    // lấy giá trị tìm kiếm nhập vào thêm vào %% để so sánh không chính xác, tức là trong database chỉ cần chứa giá trị  của query là ược
    // vd tìm 123 thì các giá trị 1234, 123,43123,123123123 ... đều được, miễn là chứa 123 là được
    $search = "%" . $_GET['query'] . "%";   

    // chuẩn bị câu truy vấn
    $stmt = $pdo->prepare("SELECT * FROM products WHERE ProductName LIKE :search");

    // thực thi câu truy vấn
    $stmt->execute(['search' => $search]);

    // lấy kết quả
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // hiển thị kết quả (duyệt kết quả, lấy ra các nội dung cần thiết để tạo thành 1 chuỗi html và show lên trang web)
    foreach ($results as $product) {
        echo '<div class="product-card">';
        echo '<img src="' . htmlspecialchars($product['ImageURLs']) . '" alt="' . htmlspecialchars($product['ProductName']) . '">';
        echo '<h3>' . htmlspecialchars($product['ProductName']) . '</h3>';
        echo '<p class="price">$' . htmlspecialchars($product['Price']) . '</p>';
        echo '<a href="information_category/information.php?productID=' . $product['ProductID'] . '" class="view-details">View Details</a>';
        echo '</div>';
    }
}
?> 