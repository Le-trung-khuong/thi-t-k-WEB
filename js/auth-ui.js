/**
 * Quản lý UI đăng nhập/đăng xuất trên thanh điều hướng
 */
document.addEventListener('DOMContentLoaded', function() {
    // Tìm phần tử nút đăng nhập
    const loginBtn = document.querySelector('.login-btn');
    
    if (!loginBtn) {
        console.error('Không tìm thấy nút đăng nhập');
        return;
    }
    
    // Hàm kiểm tra trạng thái đăng nhập
    function checkAuthStatus() {
        fetch('check_auth.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Lỗi kết nối máy chủ');
                }
                return response.json();
            })
            .then(data => {
                updateAuthUI(data);
            })
            .catch(error => {
                console.error('Lỗi kiểm tra xác thực:', error);
            });
    }
    
    // Hàm cập nhật giao diện theo trạng thái đăng nhập
    function updateAuthUI(authData) {
        if (authData.isLoggedIn) {
            // Người dùng đã đăng nhập - thay đổi nút thành dropdown
            loginBtn.innerHTML = `
                <i class="fas fa-user"></i> ${authData.username}
                <i class="fas fa-angle-down ml-1"></i>
            `;
            loginBtn.href = 'javascript:void(0)';
            loginBtn.classList.add('logged-in');
            
            // Thêm dropdown menu
            if (!document.querySelector('.auth-dropdown')) {
                const dropdownMenu = document.createElement('div');
                dropdownMenu.className = 'auth-dropdown';
                dropdownMenu.innerHTML = `
                    <div class="auth-dropdown-menu">
                        <a href="profile.php" class="auth-dropdown-item">
                            <i class="fas fa-id-card"></i> Trang cá nhân
                        </a>
                        ${authData.isAdmin ? 
                            `<a href="admin/dashboard.php" class="auth-dropdown-item">
                                <i class="fas fa-cog"></i> Quản trị
                            </a>` : ''
                        }
                        <a href="logout.php" class="auth-dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </div>
                `;
                
                // Chèn dropdown menu sau nút đăng nhập
                loginBtn.parentNode.insertBefore(dropdownMenu, loginBtn.nextSibling);
                
                // Thêm sự kiện để hiển thị/ẩn dropdown
                loginBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdownMenu.classList.toggle('active');
                });
                
                // Đóng dropdown khi click ra ngoài
                document.addEventListener('click', function(e) {
                    if (!loginBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.classList.remove('active');
                    }
                });
            }
        } else {
            // Người dùng chưa đăng nhập - giữ nút đăng nhập bình thường
            loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Đăng Nhập';
            loginBtn.href = 'login.php';
            loginBtn.classList.remove('logged-in');
            
            // Xóa dropdown menu nếu có
            const dropdownMenu = document.querySelector('.auth-dropdown');
            if (dropdownMenu) {
                dropdownMenu.remove();
            }
        }
    }
    
    // Kiểm tra trạng thái đăng nhập khi trang tải xong
    checkAuthStatus();
}); 