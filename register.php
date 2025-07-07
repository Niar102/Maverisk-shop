<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['phone'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        if (!empty($username) && !empty($email) && !empty($password) && !empty($phone)) {
            // Kiểm tra xem username hoặc email đã tồn tại
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo "<script>
                    alert('Tên đăng nhập hoặc email đã tồn tại!');
                    window.location.href = 'homepage.php';
                </script>";
            } else {
                // Mã hóa mật khẩu và thêm vào CSDL
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                // Chèn roleID với giá trị mặc định là 3
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone, roleID, address) VALUES (:username, :email, :password, :phone, :roleID, :address)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':phone', $phone);
                $roleID = 3; // Giá trị mặc định của roleID
                $stmt->bindParam(':roleID', $roleID);
                $stmt->bindParam(':address', $address);

                if ($stmt->execute()) {
                    echo "<script>
                        alert('Đăng ký thành công!');
                        window.location.href = 'homepage.php';
                    </script>";
                } else {
                    echo "<script>
                        alert('Có lỗi xảy ra, vui lòng thử lại!');
                        window.location.href = 'homepage.php';
                    </script>";
                }
            }
        } else {
            echo "Vui lòng điền đầy đủ thông tin";
        }
    }
}
?>
