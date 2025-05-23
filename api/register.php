<?php
// Thiết lập header để xử lý AJAX request
header('Content-Type: application/json');

// Kiểm tra nếu không phải là phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Phương thức không được hỗ trợ']);
    exit;
}

// Kết nối đến database
require_once '../config/db_connect.php';

// Lấy dữ liệu từ form
$data = json_decode(file_get_contents('php://input'), true);

// Nếu không lấy được JSON, thử lấy từ $_POST
if (!$data) {
    $data = $_POST;
}

// Kiểm tra dữ liệu đầu vào
if (empty($data['fullname']) || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

$fullname = trim($data['fullname']);
$email = trim($data['email']);
$password = $data['password'];

// Kiểm tra định dạng email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email không hợp lệ']);
    exit;
}

// Kiểm tra độ dài mật khẩu
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Mật khẩu phải có ít nhất 8 ký tự']);
    exit;
}

try {
    // Kiểm tra email đã tồn tại hay chưa
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetchColumn() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Email này đã được đăng ký']);
        exit;
    }
    
    // Mã hóa mật khẩu
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Thêm người dùng vào database
    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$fullname, $email, $hashedPassword]);
    
    // Trả về kết quả thành công
    echo json_encode(['success' => true, 'message' => 'Đăng ký thành công']);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Đã xảy ra lỗi, vui lòng thử lại sau']);
    
    // Log lỗi (trong thực tế nên sử dụng logging framework)
    error_log("Database Error: " . $e->getMessage());
}
?> 