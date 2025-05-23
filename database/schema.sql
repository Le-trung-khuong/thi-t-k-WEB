-- Tạo bảng cho các loại ma túy
CREATE TABLE drug_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng cho dữ liệu chi tiết tác hại ma túy
CREATE TABLE drug_effects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drug_type_id INT NOT NULL,
    effect VARCHAR(255) NOT NULL,
    is_long_term BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (drug_type_id) REFERENCES drug_types(id) ON DELETE CASCADE
);

-- Tạo bảng cho các ví dụ ma túy
CREATE TABLE drug_examples (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drug_type_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (drug_type_id) REFERENCES drug_types(id) ON DELETE CASCADE
);

-- Tạo bảng cho thông tin chi tiết về ma túy
CREATE TABLE drugs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng cho tác động ngắn/dài hạn của ma túy
CREATE TABLE drug_impacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drug_id INT NOT NULL,
    description TEXT NOT NULL,
    is_long_term BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (drug_id) REFERENCES drugs(id) ON DELETE CASCADE
);

-- Tạo bảng cho ảnh minh họa của ma túy
CREATE TABLE drug_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    drug_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drug_id) REFERENCES drugs(id) ON DELETE CASCADE
);

-- Tạo bảng cho các loại cờ bạc
CREATE TABLE gambling_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng cho các rủi ro của cờ bạc
CREATE TABLE gambling_risks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gambling_type_id INT NOT NULL,
    risk VARCHAR(255) NOT NULL,
    FOREIGN KEY (gambling_type_id) REFERENCES gambling_types(id) ON DELETE CASCADE
);

-- Tạo bảng cho các ví dụ cờ bạc
CREATE TABLE gambling_examples (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gambling_type_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (gambling_type_id) REFERENCES gambling_types(id) ON DELETE CASCADE
);

-- Tạo bảng cho thông tin chi tiết về cờ bạc
CREATE TABLE gambling_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    danger_level ENUM('low', 'medium', 'high', 'extreme') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng cho tác động ngắn/dài hạn của cờ bạc
CREATE TABLE gambling_impacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gambling_id INT NOT NULL,
    description TEXT NOT NULL,
    is_long_term BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (gambling_id) REFERENCES gambling_details(id) ON DELETE CASCADE
);

-- Tạo bảng cho câu chuyện về cờ bạc
CREATE TABLE gambling_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    avatar VARCHAR(255) NOT NULL,
    story TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
); 