<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateRole'])) {
    $userId = $_POST['UserID'];
    $newRole = $_POST['RoleID'];

    $query = "UPDATE users SET RoleID = :RoleID WHERE UserID = :UserID";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':RoleID' => $newRole,
        ':UserID' => $userId,
    ]);

    echo "<p class='text-success'>Vai trò đã được cập nhật!</p>";
    header("Location: index.html#users.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUser'])) {
    $userId = $_POST['UserID'];

    $query = "DELETE FROM users WHERE UserID = :UserID";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':UserID' => $userId]);

    echo "<p class='text-success'>Người dùng đã được xoá!</p>";
    header("Location: index.html#users.php");
}

$query = "SELECT users.*, role.Role
          FROM users 
          LEFT JOIN role ON users.RoleID = role.RoleID";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Users Management</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['UserID']) ?></td>
            <td><?= htmlspecialchars($user['Username']) ?></td>
            <td><?= htmlspecialchars($user['Email']) ?></td>
            <td>
                <?php if ($user['RoleID'] == 1): ?>
                    Admin
                <?php else: ?>
                    <form method="POST" action="users.php" class="d-inline">
                        <input type="hidden" name="UserID" value="<?= htmlspecialchars($user['UserID']) ?>">
                        <select name="RoleID" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="3" <?= $user['RoleID'] == 3 ? 'selected' : '' ?>>User</option>
                            <option value="2" <?= $user['RoleID'] == 2 ? 'selected' : '' ?>>Moderator</option>
                        </select>
                        <input type="hidden" name="updateRole" value="1">
                    </form>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($user['Phone']) ?></td>
            <td><?= htmlspecialchars($user['Address']) ?></td>
            <td>
                <form method="POST" action="users.php" class="d-inline">
                    <input type="hidden" name="UserID" value="<?= htmlspecialchars($user['UserID']) ?>">
                    <button type="submit" name="deleteUser" class="btn btn-danger btn-sm" 
                        onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
