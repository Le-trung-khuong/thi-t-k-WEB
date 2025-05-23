-- Cơ sở dữ liệu cho phần Cờ bạc

-- Bảng lưu trữ các loại hình cờ bạc
CREATE TABLE gambling_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon_class VARCHAR(50),
    image_url VARCHAR(255),
    danger_level ENUM('low', 'medium', 'high', 'extreme') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ chi tiết về các loại cờ bạc cụ thể
CREATE TABLE gambling_forms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    gambling_type_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    popularity INT DEFAULT 0, -- Mức độ phổ biến (1-10)
    image_url VARCHAR(255),
    FOREIGN KEY (gambling_type_id) REFERENCES gambling_types(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ tác hại của cờ bạc
CREATE TABLE gambling_harms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category ENUM('individual', 'family', 'society') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon_class VARCHAR(50),
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ dấu hiệu nhận biết nghiện cờ bạc
CREATE TABLE gambling_warning_signs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    stage ENUM('early', 'middle', 'severe') NOT NULL,
    sign VARCHAR(255) NOT NULL,
    description TEXT,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ biện pháp phòng tránh
CREATE TABLE gambling_prevention (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category ENUM('individual', 'family', 'society') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon_class VARCHAR(50),
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ các rủi ro từ cờ bạc trực tuyến
CREATE TABLE online_gambling_risks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon_class VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ các nguồn lực hỗ trợ người nghiện cờ bạc
CREATE TABLE gambling_support_resources (
    id INT PRIMARY KEY AUTO_INCREMENT,
    resource_type ENUM('hotline', 'center', 'document', 'group', 'app') NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    contact_info VARCHAR(255),
    link VARCHAR(255),
    icon_class VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ câu hỏi kiểm tra mức độ nghiện cờ bạc
CREATE TABLE gambling_quiz_questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question TEXT NOT NULL,
    option1 TEXT NOT NULL,
    option2 TEXT NOT NULL,
    option3 TEXT NOT NULL,
    option4 TEXT NOT NULL,
    correct_answer INT NOT NULL, -- 0-3 tương ứng với option1-4
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng lưu trữ câu chuyện hồi phục thành công
CREATE TABLE gambling_recovery_stories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    person_name VARCHAR(255) NOT NULL,
    age INT,
    avatar_url VARCHAR(255),
    story TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Thêm dữ liệu mẫu cho các loại hình cờ bạc
INSERT INTO gambling_types (name, description, icon_class, image_url, danger_level) VALUES
('Cờ Bạc Truyền Thống', 'Các hình thức cờ bạc phổ biến trong văn hóa truyền thống Việt Nam', 'fas fa-coins', 'assets/images/gambling/traditional.jpg', 'high'),
('Cờ Bạc Trực Tuyến', 'Các hoạt động cờ bạc diễn ra trên internet, dễ tiếp cận 24/7', 'fas fa-globe', 'assets/images/gambling/online.jpg', 'extreme'),
('Game Có Yếu Tố Cờ Bạc', 'Các trò chơi điện tử có tính năng may rủi, cá cược tiền thật', 'fas fa-gamepad', 'assets/images/gambling/gaming.jpg', 'high'),
('Đầu Tư Rủi Ro Cao', 'Các hoạt động đầu tư có tính chất đánh bạc, đầu cơ', 'fas fa-chart-line', 'assets/images/gambling/investment.jpg', 'medium');

-- Thêm dữ liệu mẫu cho các tác hại cờ bạc
INSERT INTO gambling_harms (category, title, description, icon_class, order_index) VALUES
('individual', 'Sức Khỏe Tâm Thần', 'Nghiện cờ bạc có thể dẫn đến trầm cảm, lo âu, rối loạn stress, thậm chí là ý định tự tử', 'fas fa-brain', 1),
('individual', 'Khủng Hoảng Tài Chính', 'Nợ nần chồng chất, mất khả năng chi trả các khoản vay, lâm vào cảnh túng thiếu và phá sản', 'fas fa-wallet', 2),
('family', 'Đổ Vỡ Hôn Nhân', 'Nhiều cuộc hôn nhân tan vỡ vì cờ bạc khi niềm tin bị phá hủy, tài sản chung bị đem đi đánh bạc', 'fas fa-heart-broken', 1),
('family', 'Ảnh Hưởng Đến Con Cái', 'Con cái của người nghiện cờ bạc có nguy cơ cao bị lạm dụng, bỏ bê và phát triển các vấn đề tâm lý', 'fas fa-child', 2),
('society', 'Tội Phạm Gia Tăng', 'Nhiều người nghiện cờ bạc tìm đến các hoạt động phạm pháp như trộm cắp, lừa đảo', 'fas fa-balance-scale', 1),
('society', 'Gánh Nặng Y Tế', 'Chi phí điều trị các vấn đề sức khỏe liên quan đến cờ bạc gây gánh nặng cho hệ thống y tế công', 'fas fa-hospital', 2);

-- Thêm dữ liệu mẫu cho các dấu hiệu cảnh báo
INSERT INTO gambling_warning_signs (stage, sign, description, order_index) VALUES
('early', 'Thường xuyên nghĩ về cờ bạc', 'Dành nhiều thời gian nghĩ về trải nghiệm cờ bạc trước đó hoặc lên kế hoạch cho lần chơi tiếp theo', 1),
('early', 'Tăng dần số tiền đánh bạc', 'Cần đánh với số tiền ngày càng lớn để cảm thấy phấn khích như trước', 2),
('middle', 'Vay mượn tiền để đánh bạc', 'Thường xuyên vay mượn tiền từ người thân, bạn bè hoặc thậm chí là tín dụng đen để có tiền chơi', 1),
('middle', 'Bỏ bê công việc, học tập vì cờ bạc', 'Sao nhãng trách nhiệm, bỏ bê công việc, học tập để dành thời gian cho cờ bạc', 2),
('severe', 'Đánh bạc lớn để gỡ lại tiền đã thua', 'Liên tục quay lại đánh bạc với số tiền lớn hơn với hy vọng gỡ lại những khoản thua trước đó', 1),
('severe', 'Bán tài sản, đồ đạc để lấy tiền đánh bạc', 'Bán hoặc cầm cố tài sản, đồ đạc cá nhân và gia đình để có tiền tiếp tục đánh bạc', 2);

-- Thêm dữ liệu mẫu cho câu hỏi kiểm tra
INSERT INTO gambling_quiz_questions (question, option1, option2, option3, option4, correct_answer, feedback) VALUES
('Đâu là dấu hiệu cảnh báo sớm của nghiện cờ bạc?', 'Chỉ chơi trong các dịp đặc biệt', 'Luôn nghĩ về cờ bạc khi không đánh bạc', 'Đặt giới hạn thời gian và tiền bạc khi chơi', 'Chỉ chơi với số tiền nhỏ', 1, 'Suy nghĩ liên tục về cờ bạc, không thể ngừng nghĩ về nó là một dấu hiệu cảnh báo sớm của nghiện cờ bạc.'),
('Cờ bạc trực tuyến được cho là nguy hiểm hơn cờ bạc truyền thống vì lý do nào?', 'Tỷ lệ thắng thấp hơn', 'Giá cược cao hơn', 'Có thể chơi bất kỳ lúc nào và không dễ bị phát hiện', 'Luôn bị gian lận', 2, 'Cờ bạc trực tuyến có thể tiếp cận 24/7, người chơi có thể đánh bạc một cách kín đáo mà không bị người khác phát hiện, làm tăng nguy cơ nghiện.'); 