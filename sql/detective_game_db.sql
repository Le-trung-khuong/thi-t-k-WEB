-- Database "Nhà Điều Tra Tệ Nạn"

-- Bảng vụ án chính
CREATE TABLE cases (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    difficulty INT NOT NULL,
    mode VARCHAR(20) NOT NULL,
    is_locked BOOLEAN DEFAULT FALSE,
    image VARCHAR(255),
    timed BOOLEAN DEFAULT FALSE,
    time_limit INT DEFAULT 0,
    success_content TEXT,
    failure_content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng điều kiện mở khóa vụ án
CREATE TABLE case_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    case_id VARCHAR(50) NOT NULL,
    required_case_id VARCHAR(50) NOT NULL,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (required_case_id) REFERENCES cases(id) ON DELETE CASCADE
);

-- Bảng bằng chứng
CREATE TABLE evidence (
    id VARCHAR(50) PRIMARY KEY,
    case_id VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    analysis TEXT,
    points INT DEFAULT 0,
    collected_by_default BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE
);

-- Bảng địa điểm
CREATE TABLE locations (
    id VARCHAR(50) PRIMARY KEY,
    case_id VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255),
    first_location BOOLEAN DEFAULT FALSE,
    locked BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE
);

-- Bảng điều kiện mở khóa địa điểm
CREATE TABLE location_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id VARCHAR(50) NOT NULL,
    required_evidence_id VARCHAR(50) NOT NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
    FOREIGN KEY (required_evidence_id) REFERENCES evidence(id) ON DELETE CASCADE
);

-- Bảng hành động ở địa điểm
CREATE TABLE location_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id VARCHAR(50) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    target_id VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL,
    icon VARCHAR(50),
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
);

-- Bảng nhân vật
CREATE TABLE characters (
    id VARCHAR(50) PRIMARY KEY,
    case_id VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100),
    image VARCHAR(255),
    initial_dialog TEXT,
    locked BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE
);

-- Bảng điều kiện mở khóa nhân vật
CREATE TABLE character_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    character_id VARCHAR(50) NOT NULL,
    requirement_type ENUM('evidence', 'character') NOT NULL,
    required_id VARCHAR(50) NOT NULL,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE
);

-- Bảng lựa chọn đối thoại nhân vật
CREATE TABLE dialog_options (
    id VARCHAR(50) PRIMARY KEY,
    character_id VARCHAR(50) NOT NULL,
    text TEXT NOT NULL,
    correct BOOLEAN DEFAULT FALSE,
    points INT DEFAULT 0,
    response TEXT,
    is_final_solution BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (character_id) REFERENCES characters(id) ON DELETE CASCADE
);

-- Bảng mở khóa từ lựa chọn đối thoại
CREATE TABLE dialog_unlocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dialog_option_id VARCHAR(50) NOT NULL,
    unlock_type ENUM('location', 'evidence', 'character') NOT NULL,
    unlocked_id VARCHAR(50) NOT NULL,
    FOREIGN KEY (dialog_option_id) REFERENCES dialog_options(id) ON DELETE CASCADE
);

-- Bảng các bước vụ án
CREATE TABLE steps (
    id VARCHAR(50) PRIMARY KEY,
    case_id VARCHAR(50) NOT NULL,
    narrative TEXT NOT NULL,
    location_id VARCHAR(50),
    is_starting_step BOOLEAN DEFAULT FALSE,
    next_step_id VARCHAR(50),
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL
);

-- Bảng điều kiện bước
CREATE TABLE step_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    step_id VARCHAR(50) NOT NULL,
    requirement_type ENUM('evidence', 'character') NOT NULL,
    required_id VARCHAR(50) NOT NULL,
    FOREIGN KEY (step_id) REFERENCES steps(id) ON DELETE CASCADE
);

-- Bảng hành động bước
CREATE TABLE step_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    step_id VARCHAR(50) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    target_id VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL,
    icon VARCHAR(50),
    FOREIGN KEY (step_id) REFERENCES steps(id) ON DELETE CASCADE
);

-- Bảng bước cuối
CREATE TABLE final_steps (
    id VARCHAR(50) PRIMARY KEY,
    case_id VARCHAR(50) NOT NULL,
    require_character_id VARCHAR(50),
    narrative TEXT NOT NULL,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE,
    FOREIGN KEY (require_character_id) REFERENCES characters(id) ON DELETE SET NULL
);

-- Bảng điều kiện bằng chứng cho bước cuối
CREATE TABLE final_step_evidence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    final_step_id VARCHAR(50) NOT NULL,
    evidence_id VARCHAR(50) NOT NULL,
    FOREIGN KEY (final_step_id) REFERENCES final_steps(id) ON DELETE CASCADE,
    FOREIGN KEY (evidence_id) REFERENCES evidence(id) ON DELETE CASCADE
);

-- Bảng lựa chọn cho bước cuối
CREATE TABLE final_choices (
    id VARCHAR(50) PRIMARY KEY,
    final_step_id VARCHAR(50) NOT NULL,
    text TEXT NOT NULL,
    correct BOOLEAN DEFAULT FALSE,
    points INT DEFAULT 0,
    outcome TEXT,
    next_case_id VARCHAR(50),
    FOREIGN KEY (final_step_id) REFERENCES final_steps(id) ON DELETE CASCADE,
    FOREIGN KEY (next_case_id) REFERENCES cases(id) ON DELETE SET NULL
);

-- Bảng kiến thức bổ ích
CREATE TABLE educational_tips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    case_id VARCHAR(50) NOT NULL,
    tip_text TEXT NOT NULL,
    order_num INT DEFAULT 0,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE
);

-- Bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE,
    score INT DEFAULT 0,
    rank VARCHAR(50) DEFAULT 'Tân binh',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng tiến trình người dùng
CREATE TABLE user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    case_id VARCHAR(50) NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    success BOOLEAN DEFAULT FALSE,
    score INT DEFAULT 0,
    correct_choices INT DEFAULT 0,
    wrong_choices INT DEFAULT 0,
    evidence_collected INT DEFAULT 0,
    time_spent INT DEFAULT 0,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE
);

-- Bảng thành tựu
CREATE TABLE achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    points INT DEFAULT 0,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng thành tựu người dùng
CREATE TABLE user_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    achievement_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
); 