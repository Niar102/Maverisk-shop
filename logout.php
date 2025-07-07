<?php
session_start();
session_destroy(); // Xóa toàn bộ session
header('Content-Type: application/json');
echo json_encode(['success' => true]);
?>
