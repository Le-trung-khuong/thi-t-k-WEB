<?php
// Bắt đầu phiên làm việc
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập thì chuyển hướng đến trang đăng nhập
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Kết nối đến database
require_once "../database/config.php";

// Lấy thông tin người dùng
$user_id = $_SESSION["user_id"];
$user = null;

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Không tìm thấy người dùng, đăng xuất
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    $error = "Lỗi kết nối: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
    <title>Trang Cá Nhân - Vì Một Cộng Đồng Khỏe Mạnh</title>
    <meta name="description" content="Quản lý thông tin cá nhân và tùy chỉnh tài khoản của bạn.">
    <link rel="stylesheet" href="../css/custom.css">
    <link rel="stylesheet" href="../css/styles-index.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link rel="stylesheet" href="../css/auth-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .profile-container {
            padding: 4rem 2rem;
            background-color: #f9f9f9;
            min-height: calc(100vh - 80px);
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .profile-email {
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .profile-role {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
            margin-top: 0.5rem;
        }
        
        .profile-card {
            background-color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .profile-card-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 0.5rem;
        }
        
        .profile-form-group {
            margin-bottom: 1.5rem;
        }
        
        .profile-form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .profile-form-control {
            width: 100%;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .profile-form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
            outline: none;
        }
        
        .profile-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .profile-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(var(--primary-rgb), 0.3);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="navbar">
                <a href="index.html" class="navbar-logo">
                    <i class="fas fa-shield-alt"></i> K91
                </a>
                <div class="navbar-right">
                    <a href="index.html" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Quay lại Trang Chủ
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="profile-container">
        <div class="container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="profile-info">
                    <h1 class="profile-name"><?php echo htmlspecialchars($user["fullname"] ?? $user["username"]); ?></h1>
                    <p class="profile-email"><?php echo htmlspecialchars($user["email"]); ?></p>
                    <span class="profile-role">
                        <?php echo $user["is_admin"] ? "Quản trị viên" : "Thành viên"; ?>
                    </span>
                </div>
            </div>
            
            <div class="row">
                <div class="col-8 col-md-12">
                    <div class="profile-card">
                        <h2 class="profile-card-title">Thông tin cá nhân</h2>
                        <form action="#" method="post">
                            <div class="profile-form-group">
                                <label class="profile-form-label">Tên đăng nhập</label>
                                <input type="text" class="profile-form-control" value="<?php echo htmlspecialchars($user["username"]); ?>" readonly>
                            </div>
                            
                            <div class="profile-form-group">
                                <label class="profile-form-label">Họ và tên</label>
                                <input type="text" class="profile-form-control" name="fullname" value="<?php echo htmlspecialchars($user["fullname"] ?? ''); ?>">
                            </div>
                            
                            <div class="profile-form-group">
                                <label class="profile-form-label">Email</label>
                                <input type="email" class="profile-form-control" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>">
                            </div>
                            
                            <button type="submit" class="profile-btn" name="update_profile">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="col-4 col-md-12">
                    <div class="profile-card">
                        <h2 class="profile-card-title">Đổi mật khẩu</h2>
                        <form action="#" method="post">
                            <div class="profile-form-group">
                                <label class="profile-form-label">Mật khẩu hiện tại</label>
                                <input type="password" class="profile-form-control" name="current_password">
                            </div>
                            
                            <div class="profile-form-group">
                                <label class="profile-form-label">Mật khẩu mới</label>
                                <input type="password" class="profile-form-control" name="new_password">
                            </div>
                            
                            <div class="profile-form-group">
                                <label class="profile-form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" class="profile-form-control" name="confirm_password">
                            </div>
                            
                            <button type="submit" class="profile-btn" name="change_password">
                                <i class="fas fa-key"></i> Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="../js/auth-ui.js"></script>
</body>
</html> 