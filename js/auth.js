// Hàm kiểm tra trạng thái đăng nhập
function checkAuthStatus() {
    const token = localStorage.getItem('auth_token') || getCookie('auth_token');
    
    if (!token) {
        return Promise.resolve({ authenticated: false });
    }
    
    return fetch('/api/check-auth.php', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`
        }
    })
    .then(response => response.json())
    .catch(error => {
        console.error('Lỗi kiểm tra đăng nhập:', error);
        return { authenticated: false };
    });
}

// Hàm đăng nhập
function login(email, password, remember) {
    return fetch('/api/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email, password, remember })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.token) {
            // Lưu token vào localStorage hoặc cookie
            localStorage.setItem('auth_token', data.token);
            
            if (remember) {
                // Lưu token vào cookie nếu chọn "Ghi nhớ đăng nhập"
                setCookie('auth_token', data.token, 7); // 7 ngày
            }
            
            // Lưu thông tin người dùng
            localStorage.setItem('user', JSON.stringify(data.user));
        }
        
        return data;
    });
}

// Hàm đăng ký
function register(fullname, email, password) {
    return fetch('/api/register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ fullname, email, password })
    })
    .then(response => response.json());
}

// Hàm đăng xuất
function logout() {
    const token = localStorage.getItem('auth_token');
    
    // Gọi API đăng xuất nếu có
    if (token) {
        fetch('/api/logout.php', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        }).catch(error => console.error('Lỗi đăng xuất:', error));
    }
    
    // Xóa token và thông tin người dùng
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    deleteCookie('auth_token');
    
    // Chuyển hướng về trang chủ
    window.location.href = '/';
}

// Hàm lấy cookie
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Hàm tạo cookie
function setCookie(name, value, days) {
    let expires = '';
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = `; expires=${date.toUTCString()}`;
    }
    document.cookie = `${name}=${value}${expires}; path=/; SameSite=Strict`;
}

// Hàm xóa cookie
function deleteCookie(name) {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
}

// Xử lý form đăng nhập khi trang được tải
document.addEventListener('DOMContentLoaded', () => {
    // Kiểm tra trạng thái đăng nhập
    checkAuthStatus().then(result => {
        if (result.authenticated) {
            // Người dùng đã đăng nhập
            const currentPath = window.location.pathname;
            
            // Nếu đang ở trang đăng nhập/đăng ký thì chuyển hướng về trang chủ
            if (currentPath.includes('/login.html') || currentPath.includes('/register.html')) {
                window.location.href = '/';
            }
            
            // Cập nhật giao diện người dùng đã đăng nhập
            updateUIForLoggedInUser(result.user);
        }
    });
    
    // Xử lý form đăng nhập
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const remember = document.getElementById('rememberMe').checked;
            
            // Hiển thị trạng thái đang xử lý
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
            
            // Ẩn thông báo lỗi cũ
            const alertBox = document.getElementById('loginAlert');
            if (alertBox) alertBox.style.display = 'none';
            
            login(email, password, remember)
                .then(data => {
                    if (data.success) {
                        // Hiển thị thông báo thành công
                        showAlert('loginAlert', 'success', data.message || 'Đăng nhập thành công!');
                        
                        // Chuyển hướng sau 1 giây
                        setTimeout(() => {
                            window.location.href = '/';
                        }, 1000);
                    } else {
                        // Hiển thị lỗi
                        showAlert('loginAlert', 'danger', data.error || 'Đã xảy ra lỗi, vui lòng thử lại sau');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Lỗi đăng nhập:', error);
                    showAlert('loginAlert', 'danger', 'Đã xảy ra lỗi, vui lòng thử lại sau');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
        });
    }
    
    // Xử lý form đăng ký
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const fullname = document.getElementById('registerFullname').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            // Kiểm tra mật khẩu xác nhận
            if (password !== confirmPassword) {
                showAlert('registerAlert', 'danger', 'Mật khẩu xác nhận không khớp');
                return;
            }
            
            // Hiển thị trạng thái đang xử lý
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...';
            
            // Ẩn thông báo lỗi cũ
            const alertBox = document.getElementById('registerAlert');
            if (alertBox) alertBox.style.display = 'none';
            
            register(fullname, email, password)
                .then(data => {
                    if (data.success) {
                        // Hiển thị thông báo thành công
                        showAlert('registerAlert', 'success', data.message || 'Đăng ký thành công! Vui lòng đăng nhập.');
                        
                        // Đặt lại form
                        registerForm.reset();
                        
                        // Chuyển hướng đến trang đăng nhập sau 2 giây
                        setTimeout(() => {
                            window.location.href = '/html/login.html';
                        }, 2000);
                    } else {
                        // Hiển thị lỗi
                        showAlert('registerAlert', 'danger', data.error || 'Đã xảy ra lỗi, vui lòng thử lại sau');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                    }
                })
                .catch(error => {
                    console.error('Lỗi đăng ký:', error);
                    showAlert('registerAlert', 'danger', 'Đã xảy ra lỗi, vui lòng thử lại sau');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
        });
    }
    
    // Xử lý nút đăng xuất
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logout();
        });
    }
});

// Hàm hiển thị thông báo
function showAlert(elementId, type, message) {
    const alertBox = document.getElementById(elementId);
    if (alertBox) {
        alertBox.className = `alert alert-${type}`;
        alertBox.innerHTML = message;
        alertBox.style.display = 'block';
    }
}

// Hàm cập nhật giao diện khi đã đăng nhập
function updateUIForLoggedInUser(user) {
    // Cập nhật menu
    const authMenu = document.getElementById('authMenu');
    if (authMenu) {
        authMenu.innerHTML = `
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-1"></i> ${user.fullname}
                </a>
                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="/html/profile.html">Thông tin cá nhân</a></li>
                    ${user.role === 'admin' ? '<li><a class="dropdown-item" href="/admin/">Quản trị</a></li>' : ''}
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" id="logoutBtn">Đăng xuất</a></li>
                </ul>
            </li>
        `;
        
        // Gắn sự kiện đăng xuất
        document.getElementById('logoutBtn').addEventListener('click', function(e) {
            e.preventDefault();
            logout();
        });
    }
} 