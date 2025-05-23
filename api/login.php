<?php
// Thiết lập header cho AJAX request
header('Content-Type: application/json');

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Phương thức không được hỗ trợ']);
    exit;
}

// Kết nối đến database
require_once '../config/db_connect.php';

// Lấy dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);

// Nếu không lấy được JSON, thử lấy từ $_POST
if (!$data) {
    $data = $_POST;
}

// Kiểm tra dữ liệu đầu vào
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Vui lòng nhập email và mật khẩu']);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];
$rememberMe = isset($data['remember']) && $data['remember'] ? true : false;

try {
    // Tìm người dùng theo email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Kiểm tra người dùng tồn tại và mật khẩu đúng
    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Email hoặc mật khẩu không đúng']);
        exit;
    }
    
    // Kiểm tra tài khoản có bị khóa không
    if (!$user['is_active']) {
        http_response_code(403);
        echo json_encode(['error' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên']);
        exit;
    }
    
    // Cập nhật thời gian đăng nhập cuối
    $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->execute([$user['id']]);
    
    // Tạo token JWT (đơn giản hóa)
    $tokenExpiresAt = time() + ($rememberMe ? 7 * 24 * 60 * 60 : 24 * 60 * 60);
    $tokenPayload = [
        'user_id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'exp' => $tokenExpiresAt
    ];
    $tokenString = base64_encode(json_encode($tokenPayload));
    $signature = hash_hmac('sha256', $tokenString, 'vicongdongkhoemanh-secret-key');
    $token = $tokenString . '.' . $signature;
    
    // Lưu session
    $expiryDate = date('Y-m-d H:i:s', $tokenExpiresAt);
    $sessionStmt = $pdo->prepare("INSERT INTO user_sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
    $sessionStmt->execute([$user['id'], $token, $expiryDate]);
    
    // Trả về thông tin người dùng và token
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'fullname' => $user['fullname'],
            'role' => $user['role']
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Đã xảy ra lỗi, vui lòng thử lại sau']);
    error_log("Database Error: " . $e->getMessage());
}
?> 