<?php
session_start(); // Khởi tạo session

require_once 'db.php'; // Đảm bảo đường dẫn đúng tới tệp db.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']); // Lấy tên đăng nhập
        $password = trim($_POST['password']); // Lấy mật khẩu

        if (!empty($username) && !empty($password)) { // Kiểm tra nếu tên đăng nhập và mật khẩu không trống
            // Truy vấn cơ sở dữ liệu để lấy thông tin người dùng
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username"); // Lấy thông tin người dùng theo username
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            // Kiểm tra nếu người dùng tồn tại
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo '<pre>';
            print_r($user); // Xem kết quả của fetch()
            echo '</pre>';
            if ($user) {
                // So sánh mật khẩu người dùng nhập vào với mật khẩu trong cơ sở dữ liệu (mã hóa mật khẩu)
                if ($user && password_verify($password, $user['Password'])) {
                    $_SESSION['user'] = $user; // Lưu thông tin người dùng vào session
                    header("Location: homepage.php");
                    exit; // Dừng script
                } else {
                    echo "<script>
                        alert('Sai mật khẩu!');
                        window.location.href = 'homepage.php';
                    </script>";
                }
            } else {
                echo "<script>
                    alert('Không tìm thấy tên đăng nhập!');
                    window.location.href = 'homepage.php';
                </script>";
            }
        } else {
            echo "<script>
                alert('Vui lòng điền đầy đủ thông tin!');
                window.location.href = 'homepage.php';
            </script>";
        }
    }
}

