<?php
// Thiết lập header cho AJAX request
header('Content-Type: application/json');

// Kết nối đến database
require_once '../config/db_connect.php';

// Lấy token từ header Authorization hoặc cookie
$token = null;

// Kiểm tra header Authorization (Bearer token)
if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    if (strpos($authHeader, 'Bearer ') === 0) {
        $token = substr($authHeader, 7);
    }
} 
// Nếu không có trong header, kiểm tra trong cookie
else if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
}

// Kiểm tra xem có token không
if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'Không tìm thấy token xác thực', 'authenticated' => false]);
    exit;
}

try {
    // Tách token
    $tokenParts = explode('.', $token);
    
    if (count($tokenParts) !== 2) {
        throw new Exception("Token không hợp lệ");
    }
    
    $tokenString = $tokenParts[0];
    $signature = $tokenParts[1];
    
    // Xác thực chữ ký
    $expectedSignature = hash_hmac('sha256', $tokenString, 'vicongdongkhoemanh-secret-key');
    
    if (!hash_equals($expectedSignature, $signature)) {
        throw new Exception("Chữ ký token không hợp lệ");
    }
    
    // Giải mã payload
    $payload = json_decode(base64_decode($tokenString), true);
    
    if (!$payload || !isset($payload['user_id']) || !isset($payload['exp'])) {
        throw new Exception("Token không chứa đủ thông tin");
    }
    
    // Kiểm tra hết hạn
    if ($payload['exp'] < time()) {
        throw new Exception("Token đã hết hạn");
    }
    
    // Kiểm tra token trong database
    $stmt = $pdo->prepare("
        SELECT us.*, u.fullname, u.email, u.role, u.is_active
        FROM user_sessions us
        JOIN users u ON us.user_id = u.id
        WHERE us.token = ? AND us.is_active = 1 AND us.expires_at > NOW()
    ");
    $stmt->execute([$token]);
    $session = $stmt->fetch();
    
    if (!$session) {
        throw new Exception("Phiên đăng nhập không tồn tại hoặc đã hết hạn");
    }
    
    // Kiểm tra tài khoản có bị khóa không
    if (!$session['is_active']) {
        throw new Exception("Tài khoản đã bị khóa");
    }
    
    // Trả về thông tin người dùng
    echo json_encode([
        'authenticated' => true,
        'user' => [
            'id' => $session['user_id'],
            'email' => $session['email'],
            'fullname' => $session['fullname'],
            'role' => $session['role']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'error' => $e->getMessage(),
        'authenticated' => false
    ]);
}
?> 