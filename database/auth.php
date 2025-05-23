<?php
// Kết nối đến database
require_once "config.php";

// Bắt đầu phiên làm việc nếu chưa được bắt đầu
function start_session_if_not_started() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Kiểm tra người dùng đã đăng nhập chưa
function is_logged_in() {
    start_session_if_not_started();
    return isset($_SESSION['user_id']);
}

// Lấy thông tin người dùng hiện tại
function get_current_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT id, username, email, fullname, avatar, bio, is_admin, status, email_verified, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        }
    } catch (PDOException $e) {
        error_log("Lỗi lấy thông tin người dùng: " . $e->getMessage());
    }
    
    return null;
}

// Kiểm tra người dùng có phải admin không
function is_admin() {
    if (!is_logged_in()) {
        return false;
    }
    
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Đăng nhập người dùng
function login_user($user_id, $username, $is_admin = 0) {
    start_session_if_not_started();
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['is_admin'] = $is_admin;
    
    // Cập nhật thời gian đăng nhập cuối
    global $conn;
    
    try {
        $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        
        // Ghi log đăng nhập
        log_user_action($user_id, 'login');
        
        // Tạo phiên đăng nhập
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at, ip_address, user_agent) VALUES (:user_id, :token, :expires, :ip, :user_agent)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires', $expires);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':user_agent', $user_agent);
        $stmt->execute();
        
        // Lưu token vào cookie để tự động đăng nhập
        if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
            setcookie('remember_token', $token, strtotime('+30 days'), '/', '', false, true);
        }
        
    } catch (PDOException $e) {
        error_log("Lỗi đăng nhập: " . $e->getMessage());
    }
}

// Đăng xuất người dùng
function logout_user() {
    start_session_if_not_started();
    
    // Ghi log đăng xuất nếu đang đăng nhập
    if (isset($_SESSION['user_id'])) {
        log_user_action($_SESSION['user_id'], 'logout');
        
        // Xóa phiên đăng nhập
        if (isset($_COOKIE['remember_token'])) {
            global $conn;
            $token = $_COOKIE['remember_token'];
            
            try {
                $stmt = $conn->prepare("DELETE FROM user_sessions WHERE session_token = :token");
                $stmt->bindParam(':token', $token);
                $stmt->execute();
            } catch (PDOException $e) {
                error_log("Lỗi xóa phiên đăng nhập: " . $e->getMessage());
            }
            
            // Xóa cookie
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
    }
    
    // Hủy phiên
    session_unset();
    session_destroy();
}

// Ghi log hành động của người dùng
function log_user_action($user_id, $action) {
    global $conn;
    
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO user_logs (user_id, action, ip_address, user_agent) VALUES (:user_id, :action, :ip, :user_agent)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':user_agent', $user_agent);
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Lỗi ghi log: " . $e->getMessage());
    }
}

// Kiểm tra phiên đăng nhập tự động từ cookie
function check_remember_login() {
    if (is_logged_in()) {
        return true;
    }
    
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        global $conn;
        
        try {
            $stmt = $conn->prepare("
                SELECT s.user_id, u.username, u.is_admin
                FROM user_sessions s
                JOIN users u ON s.user_id = u.id
                WHERE s.session_token = :token
                AND s.expires_at > NOW()
                AND u.status = 'active'
            ");
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch();
                login_user($row['user_id'], $row['username'], $row['is_admin']);
                return true;
            } else {
                // Token không hợp lệ hoặc đã hết hạn
                setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            }
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra phiên đăng nhập: " . $e->getMessage());
        }
    }
    
    return false;
}

// Tạo mã xác minh email
function create_email_verification($user_id) {
    global $conn;
    
    try {
        $token = bin2hex(random_bytes(32));
        
        $stmt = $conn->prepare("UPDATE users SET verification_token = :token WHERE id = :id");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        
        return $token;
    } catch (PDOException $e) {
        error_log("Lỗi tạo mã xác minh email: " . $e->getMessage());
        return false;
    }
}

// Xác minh email
function verify_email($token) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            $stmt = $conn->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = :id");
            $stmt->bindParam(':id', $user['id']);
            $stmt->execute();
            
            log_user_action($user['id'], 'verify_email');
            
            return true;
        }
    } catch (PDOException $e) {
        error_log("Lỗi xác minh email: " . $e->getMessage());
    }
    
    return false;
}

// Tạo yêu cầu đặt lại mật khẩu
function create_password_reset($email) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email AND status = 'active'");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Xóa các yêu cầu đặt lại mật khẩu cũ
            $stmt = $conn->prepare("DELETE FROM reset_password WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
            
            // Tạo yêu cầu mới
            $stmt = $conn->prepare("INSERT INTO reset_password (user_id, reset_token, expires_at) VALUES (:user_id, :token, :expires)");
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expires', $expires);
            $stmt->execute();
            
            log_user_action($user['id'], 'request_password_reset');
            
            return $token;
        }
    } catch (PDOException $e) {
        error_log("Lỗi tạo yêu cầu đặt lại mật khẩu: " . $e->getMessage());
    }
    
    return false;
}

// Kiểm tra token đặt lại mật khẩu
function verify_reset_token($token) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT r.user_id 
            FROM reset_password r
            JOIN users u ON r.user_id = u.id
            WHERE r.reset_token = :token 
            AND r.expires_at > NOW()
            AND u.status = 'active'
        ");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch()['user_id'];
        }
    } catch (PDOException $e) {
        error_log("Lỗi kiểm tra token đặt lại mật khẩu: " . $e->getMessage());
    }
    
    return false;
}

// Đặt lại mật khẩu
function reset_password($user_id, $new_password) {
    global $conn;
    
    try {
        // Mã hóa mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu
        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        
        // Xóa yêu cầu đặt lại mật khẩu
        $stmt = $conn->prepare("DELETE FROM reset_password WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        // Xóa tất cả phiên đăng nhập
        $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        log_user_action($user_id, 'reset_password');
        
        return true;
    } catch (PDOException $e) {
        error_log("Lỗi đặt lại mật khẩu: " . $e->getMessage());
    }
    
    return false;
}

// Kiểm tra phiên đăng nhập tự động khi load file
check_remember_login();
?> 