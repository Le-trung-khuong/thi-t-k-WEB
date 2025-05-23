<?php
/**
 * Kết nối database cho trò chơi Nhà Điều Tra Tệ Nạn
 */

// Thông tin kết nối database
$host = '127.0.0.1';    // hoặc 'localhost'
$user = 'root';         // user mặc định
$pass = '';             // để trống nếu chưa đặt mật khẩu
$db   = 'web_k91';      // tên database bạn vừa tạo

// Thiết lập kết nối PDO
try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );

    // Đặt charset UTF-8
    $pdo->exec("SET NAMES utf8mb4");

} catch (PDOException $e) {
    // Xử lý lỗi kết nối
    die(json_encode([
        'success' => false,
        'message' => 'Không thể kết nối đến database: ' . $e->getMessage()
    ]));
}

/**
 * Hàm trả về kết quả JSON
 * 
 * @param array $data Dữ liệu trả về
 * @param int   $status_code Mã trạng thái HTTP
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
