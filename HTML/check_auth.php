<?php
// Khởi động phiên làm việc
session_start();

// Thiết lập header JSON
header('Content-Type: application/json');

// Mảng kết quả
$result = [
    'isLoggedIn' => false,
    'username' => null,
    'isAdmin' => false
];

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $result['isLoggedIn'] = true;
    $result['username'] = $_SESSION['username'];
    
    // Nếu có thông tin về quyền admin
    if (isset($_SESSION['is_admin'])) {
        $result['isAdmin'] = (bool)$_SESSION['is_admin'];
    }
}

// Trả về kết quả dạng JSON
echo json_encode($result);
?> 