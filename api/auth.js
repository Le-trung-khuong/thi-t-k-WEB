const express = require('express');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { pool } = require('../config/database');

const router = express.Router();

// Đăng ký tài khoản
router.post('/register', async (req, res) => {
    try {
        const { fullName, email, password } = req.body;
        
        if (!fullName || !email || !password) {
            return res.status(400).json({ message: 'Vui lòng điền đầy đủ thông tin' });
        }

        // Kiểm tra định dạng email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return res.status(400).json({ message: 'Địa chỉ email không hợp lệ' });
        }
        
        // Kiểm tra mật khẩu mạnh
        if (password.length < 8) {
            return res.status(400).json({ message: 'Mật khẩu phải có ít nhất 8 ký tự' });
        }
        
        // Kiểm tra email đã tồn tại chưa
        const checkEmailQuery = 'SELECT * FROM Users WHERE Email = @Email';
        const checkResult = await pool.request()
            .input('Email', email)
            .query(checkEmailQuery);
        
        if (checkResult.recordset.length > 0) {
            return res.status(400).json({ message: 'Email này đã được đăng ký' });
        }
        
        // Mã hóa mật khẩu
        const salt = await bcrypt.genSalt(10);
        const hashedPassword = await bcrypt.hash(password, salt);
        
        // Thêm người dùng vào CSDL
        const insertQuery = `
            INSERT INTO Users (Email, Password, FullName, Role)
            VALUES (@Email, @Password, @FullName, 'user')
        `;
        
        await pool.request()
            .input('Email', email)
            .input('Password', hashedPassword)
            .input('FullName', fullName)
            .query(insertQuery);
        
        return res.status(201).json({ message: 'Đăng ký thành công' });
    } catch (error) {
        console.error('Register error:', error);
        return res.status(500).json({ message: 'Đã xảy ra lỗi, vui lòng thử lại sau' });
    }
});

// Đăng nhập
router.post('/login', async (req, res) => {
    try {
        const { email, password, rememberMe } = req.body;
        
        if (!email || !password) {
            return res.status(400).json({ message: 'Vui lòng nhập email và mật khẩu' });
        }
        
        // Tìm người dùng theo email
        const query = 'SELECT * FROM Users WHERE Email = @Email';
        const result = await pool.request()
            .input('Email', email)
            .query(query);
        
        if (result.recordset.length === 0) {
            return res.status(401).json({ message: 'Email hoặc mật khẩu không đúng' });
        }
        
        const user = result.recordset[0];
        
        // Kiểm tra mật khẩu
        const isPasswordValid = await bcrypt.compare(password, user.Password);
        if (!isPasswordValid) {
            return res.status(401).json({ message: 'Email hoặc mật khẩu không đúng' });
        }
        
        // Kiểm tra tài khoản có bị khóa không
        if (!user.IsActive) {
            return res.status(401).json({ message: 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên' });
        }
        
        // Cập nhật thời gian đăng nhập cuối cùng
        await pool.request()
            .input('UserID', user.UserID)
            .query('UPDATE Users SET LastLogin = GETDATE() WHERE UserID = @UserID');
        
        // Tạo JWT token
        const tokenExpiration = rememberMe ? '7d' : '1d'; // 7 ngày nếu chọn "Ghi nhớ đăng nhập", 1 ngày nếu không
        const token = jwt.sign(
            { id: user.UserID, email: user.Email, role: user.Role },
            process.env.JWT_SECRET || 'vicongdongkhoemanh-secret-key',
            { expiresIn: tokenExpiration }
        );
        
        // Lưu phiên đăng nhập mới
        const expiresAt = new Date();
        expiresAt.setDate(expiresAt.getDate() + (rememberMe ? 7 : 1));
        
        await pool.request()
            .input('UserID', user.UserID)
            .input('Token', token)
            .input('ExpiresAt', expiresAt)
            .query(`
                INSERT INTO UserSessions (UserID, Token, ExpiresAt)
                VALUES (@UserID, @Token, @ExpiresAt)
            `);
        
        return res.json({
            message: 'Đăng nhập thành công',
            token,
            user: {
                id: user.UserID,
                email: user.Email,
                fullName: user.FullName,
                role: user.Role
            }
        });
    } catch (error) {
        console.error('Login error:', error);
        return res.status(500).json({ message: 'Đã xảy ra lỗi, vui lòng thử lại sau' });
    }
});

// Kiểm tra trạng thái đăng nhập
router.get('/me', async (req, res) => {
    try {
        // Lấy token từ header
        const token = req.headers.authorization?.split(' ')[1];
        if (!token) {
            return res.status(401).json({ message: 'Không tìm thấy token xác thực' });
        }
        
        // Xác thực token
        const decoded = jwt.verify(token, process.env.JWT_SECRET || 'vicongdongkhoemanh-secret-key');
        
        // Kiểm tra session còn hiệu lực không
        const sessionQuery = `
            SELECT * FROM UserSessions 
            WHERE Token = @Token AND IsActive = 1 AND ExpiresAt > GETDATE()
        `;
        
        const sessionResult = await pool.request()
            .input('Token', token)
            .query(sessionQuery);
        
        if (sessionResult.recordset.length === 0) {
            return res.status(401).json({ message: 'Phiên đăng nhập không hợp lệ hoặc đã hết hạn' });
        }
        
        // Lấy thông tin người dùng
        const userQuery = 'SELECT UserID, Email, FullName, Role FROM Users WHERE UserID = @UserID AND IsActive = 1';
        const userResult = await pool.request()
            .input('UserID', decoded.id)
            .query(userQuery);
        
        if (userResult.recordset.length === 0) {
            return res.status(404).json({ message: 'Không tìm thấy thông tin người dùng' });
        }
        
        const user = userResult.recordset[0];
        return res.json({
            user: {
                id: user.UserID,
                email: user.Email,
                fullName: user.FullName,
                role: user.Role
            }
        });
    } catch (error) {
        console.error('Auth verification error:', error);
        return res.status(401).json({ message: 'Token không hợp lệ hoặc đã hết hạn' });
    }
});

// Đăng xuất
router.post('/logout', async (req, res) => {
    try {
        const token = req.headers.authorization?.split(' ')[1];
        if (!token) {
            return res.status(400).json({ message: 'Không tìm thấy token' });
        }
        
        // Đánh dấu session là không còn hoạt động
        await pool.request()
            .input('Token', token)
            .query('UPDATE UserSessions SET IsActive = 0 WHERE Token = @Token');
        
        return res.json({ message: 'Đăng xuất thành công' });
    } catch (error) {
        console.error('Logout error:', error);
        return res.status(500).json({ message: 'Đã xảy ra lỗi khi đăng xuất' });
    }
});

module.exports = router; 