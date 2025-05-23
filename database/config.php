<?php
// Thông tin kết nối cơ sở dữ liệu
$db_host = "localhost";
$db_name = "social_issues_db";
$db_user = "root";
$db_pass = "123456";

// Tạo kết nối PDO
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    // Thiết lập chế độ lỗi
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Thiết lập chế độ trả về dữ liệu
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}

// Hàm upload ảnh
function uploadImage($file, $folder = "uploads") {
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
    
    // Tạo tên file ngẫu nhiên để tránh trùng lặp
    $ext = pathinfo($file["name"], PATHINFO_EXTENSION);
    $filename = uniqid() . "." . $ext;
    $target_path = $folder . "/" . $filename;
    
    // Upload file
    if (move_uploaded_file($file["tmp_name"], $target_path)) {
        return $target_path;
    }
    
    return false;
}
?> 