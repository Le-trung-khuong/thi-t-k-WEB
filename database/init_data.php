<?php
// Kết nối đến cơ sở dữ liệu
require_once "config.php";

// Xóa dữ liệu cũ (nếu có)
try {
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    $conn->exec("TRUNCATE TABLE gambling_types");
    $conn->exec("TRUNCATE TABLE gambling_risks");
    $conn->exec("TRUNCATE TABLE gambling_examples");
    $conn->exec("TRUNCATE TABLE gambling_details");
    $conn->exec("TRUNCATE TABLE gambling_impacts");
    $conn->exec("TRUNCATE TABLE gambling_stories");
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "Đã xóa dữ liệu cũ thành công.<br>";
} catch (PDOException $e) {
    die("Lỗi khi xóa dữ liệu cũ: " . $e->getMessage());
}

// Thêm dữ liệu mẫu cho gambling_types
try {
    // Tạo thư mục uploads nếu chưa tồn tại
    if (!file_exists("../uploads/gambling")) {
        mkdir("../uploads/gambling", 0777, true);
    }
    
    // Loại cờ bạc 1: Cờ Bạc Truyền Thống
    $conn->exec("INSERT INTO gambling_types (id, type, description, image) VALUES 
        (1, 'Cờ Bạc Truyền Thống', 'Các hình thức cờ bạc phổ biến trong văn hóa truyền thống Việt Nam', 'uploads/gambling/sample1.jpg')");
    
    // Thêm rủi ro
    $risks = ['Thua lỗ tài chính', 'Ảnh hưởng đến gia đình', 'Phạm tội hình sự', 'Nghiện cờ bạc'];
    foreach ($risks as $risk) {
        $stmt = $conn->prepare("INSERT INTO gambling_risks (gambling_type_id, risk) VALUES (1, :risk)");
        $stmt->bindParam(":risk", $risk);
        $stmt->execute();
    }
    
    // Thêm ví dụ
    $examples = ['Đánh bài', 'Tài xỉu', 'Lô đề', 'Cá độ thể thao', 'Xổ số'];
    foreach ($examples as $example) {
        $stmt = $conn->prepare("INSERT INTO gambling_examples (gambling_type_id, name) VALUES (1, :name)");
        $stmt->bindParam(":name", $example);
        $stmt->execute();
    }
    
    // Loại cờ bạc 2: Cờ Bạc Trực Tuyến
    $conn->exec("INSERT INTO gambling_types (id, type, description, image) VALUES 
        (2, 'Cờ Bạc Trực Tuyến', 'Các hoạt động cờ bạc diễn ra trên internet, dễ tiếp cận 24/7', 'uploads/gambling/sample2.jpg')");
    
    // Thêm rủi ro
    $risks = ['Dễ nghiện do tiếp cận 24/7', 'Khó kiểm soát chi tiêu', 'Bẫy lừa đảo', 'Thông tin cá nhân bị đánh cắp'];
    foreach ($risks as $risk) {
        $stmt = $conn->prepare("INSERT INTO gambling_risks (gambling_type_id, risk) VALUES (2, :risk)");
        $stmt->bindParam(":risk", $risk);
        $stmt->execute();
    }
    
    // Thêm ví dụ
    $examples = ['Casino trực tuyến', 'Cá cược thể thao online', 'Poker online', 'Lô đề qua app', 'Game bài đổi thưởng'];
    foreach ($examples as $example) {
        $stmt = $conn->prepare("INSERT INTO gambling_examples (gambling_type_id, name) VALUES (2, :name)");
        $stmt->bindParam(":name", $example);
        $stmt->execute();
    }
    
    // Loại cờ bạc 3: Game Có Yếu Tố Cờ Bạc
    $conn->exec("INSERT INTO gambling_types (id, type, description, image) VALUES 
        (3, 'Game Có Yếu Tố Cờ Bạc', 'Các trò chơi điện tử có tính năng may rủi, cá cược tiền thật', 'uploads/gambling/sample3.jpg')");
    
    // Thêm rủi ro
    $risks = ['Thu hút giới trẻ', 'Ranh giới mờ nhạt với game thường', 'Rủi ro tài chính', 'Cơ chế gây nghiện cao'];
    foreach ($risks as $risk) {
        $stmt = $conn->prepare("INSERT INTO gambling_risks (gambling_type_id, risk) VALUES (3, :risk)");
        $stmt->bindParam(":risk", $risk);
        $stmt->execute();
    }
    
    // Thêm ví dụ
    $examples = ['Loot box', 'Game quay thưởng', 'Game bắn cá', 'Game mạng xã hội', 'NFT Gaming'];
    foreach ($examples as $example) {
        $stmt = $conn->prepare("INSERT INTO gambling_examples (gambling_type_id, name) VALUES (3, :name)");
        $stmt->bindParam(":name", $example);
        $stmt->execute();
    }
    
    // Loại cờ bạc 4: Đầu Tư Rủi Ro Cao
    $conn->exec("INSERT INTO gambling_types (id, type, description, image) VALUES 
        (4, 'Đầu Tư Rủi Ro Cao', 'Các hoạt động đầu tư có tính chất đánh bạc, đầu cơ', 'uploads/gambling/sample4.jpg')");
    
    // Thêm rủi ro
    $risks = ['Mất vốn cao', 'Tâm lý FOMO', 'Hệ thống Ponzi', 'Đầu tư không hiểu biết'];
    foreach ($risks as $risk) {
        $stmt = $conn->prepare("INSERT INTO gambling_risks (gambling_type_id, risk) VALUES (4, :risk)");
        $stmt->bindParam(":risk", $risk);
        $stmt->execute();
    }
    
    // Thêm ví dụ
    $examples = ['Binary Option', 'Tiền số rủi ro cao', 'Forex không hiểu biết', 'Đầu tư đa cấp', 'Đầu cơ sàn ảo'];
    foreach ($examples as $example) {
        $stmt = $conn->prepare("INSERT INTO gambling_examples (gambling_type_id, name) VALUES (4, :name)");
        $stmt->bindParam(":name", $example);
        $stmt->execute();
    }
    
    echo "Đã thêm dữ liệu mẫu cho gambling_types thành công.<br>";
} catch (PDOException $e) {
    die("Lỗi khi thêm dữ liệu mẫu cho gambling_types: " . $e->getMessage());
}

// Thêm dữ liệu mẫu cho gambling_details
try {
    // Chi tiết 1: Đánh bài
    $conn->exec("INSERT INTO gambling_details (id, name, type, description, image, danger_level) VALUES 
        (1, 'Đánh bài', 'Cờ Bạc Truyền Thống', 'Các loại hình đánh bài với tiền cược, phổ biến tại Việt Nam như tiến lên, phỏm, bài cào...', 
        'uploads/gambling/detail1.jpg', 'high')");
    
    // Thêm tác động ngắn hạn
    $shortTerm = [
        'Kích thích cảm giác hưng phấn, giải trí',
        'Tạo cảm giác kết nối xã hội tạm thời',
        'Có thể được lợi nhuận ngắn hạn',
        'Tạo cảm giác thành công khi thắng',
        'Tăng cảm giác tự tin tạm thời'
    ];
    foreach ($shortTerm as $effect) {
        $stmt = $conn->prepare("INSERT INTO gambling_impacts (gambling_id, description, is_long_term) VALUES (1, :description, 0)");
        $stmt->bindParam(":description", $effect);
        $stmt->execute();
    }
    
    // Thêm tác động dài hạn
    $longTerm = [
        'Thua lỗ tài chính nghiêm trọng',
        'Ảnh hưởng đến công việc, học tập',
        'Tâm lý căng thẳng, trầm cảm',
        'Mâu thuẫn, đổ vỡ gia đình',
        'Nợ nần dẫn đến các hành vi phạm pháp'
    ];
    foreach ($longTerm as $effect) {
        $stmt = $conn->prepare("INSERT INTO gambling_impacts (gambling_id, description, is_long_term) VALUES (1, :description, 1)");
        $stmt->bindParam(":description", $effect);
        $stmt->execute();
    }
    
    // Chi tiết 2: Casino trực tuyến
    $conn->exec("INSERT INTO gambling_details (id, name, type, description, image, danger_level) VALUES 
        (2, 'Casino trực tuyến', 'Cờ Bạc Trực Tuyến', 'Các sòng bài trực tuyến cung cấp nhiều trò chơi casino như: baccarat, roulette, blackjack, slot machine...', 
        'uploads/gambling/detail2.jpg', 'extreme')");
    
    // Thêm tác động ngắn hạn
    $shortTerm = [
        'Dễ tiếp cận mọi lúc, mọi nơi',
        'Các khuyến mãi, tiền thưởng hấp dẫn ban đầu',
        'Giao diện đẹp, âm thanh sống động tạo cảm giác thật',
        'Tiện lợi khi chơi tại nhà, không cần di chuyển',
        'Nhiều lựa chọn trò chơi đa dạng'
    ];
    foreach ($shortTerm as $effect) {
        $stmt = $conn->prepare("INSERT INTO gambling_impacts (gambling_id, description, is_long_term) VALUES (2, :description, 0)");
        $stmt->bindParam(":description", $effect);
        $stmt->execute();
    }
    
    // Thêm tác động dài hạn
    $longTerm = [
        'Khó kiểm soát thời gian và tiền bạc',
        'Không gian ảo dễ mất kết nối với thực tế',
        'Nguy cơ lộ thông tin cá nhân, tài khoản ngân hàng',
        'Rủi ro cao với các trang web không uy tín',
        'Khả năng nghiện cao hơn do dễ tiếp cận 24/7'
    ];
    foreach ($longTerm as $effect) {
        $stmt = $conn->prepare("INSERT INTO gambling_impacts (gambling_id, description, is_long_term) VALUES (2, :description, 1)");
        $stmt->bindParam(":description", $effect);
        $stmt->execute();
    }
    
    echo "Đã thêm dữ liệu mẫu cho gambling_details thành công.<br>";
} catch (PDOException $e) {
    die("Lỗi khi thêm dữ liệu mẫu cho gambling_details: " . $e->getMessage());
}

// Thêm dữ liệu mẫu cho gambling_stories
try {
    // Tạo thư mục avatars nếu chưa tồn tại
    if (!file_exists("../uploads/gambling/avatars")) {
        mkdir("../uploads/gambling/avatars", 0777, true);
    }
    
    // Câu chuyện 1
    $conn->exec("INSERT INTO gambling_stories (id, name, age, avatar, story) VALUES 
        (1, 'Hoàng M.', 42, 'uploads/gambling/avatars/avatar1.jpg', 
        'Tôi đã từng đánh bạc trong 15 năm và mất gần như tất cả: gia đình, công việc, nhà cửa. Chỉ khi nhận được sự hỗ trợ từ các chuyên gia và gia đình, tôi mới dần hồi phục. Giờ đây, sau 3 năm không đánh bạc, cuộc sống của tôi đã hoàn toàn thay đổi.')");
    
    // Câu chuyện 2
    $conn->exec("INSERT INTO gambling_stories (id, name, age, avatar, story) VALUES 
        (2, 'Thu H.', 35, 'uploads/gambling/avatars/avatar2.jpg', 
        'Chồng tôi nghiện cờ bạc online, từ một người chồng tốt trở thành con người khác hẳn. Anh ấy vay nợ khắp nơi, bán cả đồ trong nhà để đánh bạc. Gia đình chúng tôi suýt tan vỡ. May mắn là sau 2 năm điều trị, anh ấy đã cai được và chúng tôi đang từng bước xây dựng lại cuộc sống.')");
    
    echo "Đã thêm dữ liệu mẫu cho gambling_stories thành công.<br>";
} catch (PDOException $e) {
    die("Lỗi khi thêm dữ liệu mẫu cho gambling_stories: " . $e->getMessage());
}

// Tạo các hình ảnh mẫu
try {
    // Function tạo file ảnh mẫu
    function createSampleImage($path, $width, $height, $color, $text) {
        $image = imagecreatetruecolor($width, $height);
        
        // Màu nền
        $bgColor = imagecolorallocate($image, hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));
        imagefill($image, 0, 0, $bgColor);
        
        // Màu chữ
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Vẽ chữ
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $textHeight = imagefontheight($fontSize);
        
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2;
        
        imagestring($image, $fontSize, $x, $y, $text, $textColor);
        
        // Lưu ảnh
        imagejpeg($image, $path, 90);
        
        // Giải phóng bộ nhớ
        imagedestroy($image);
    }
    
    // Tạo ảnh mẫu cho loại cờ bạc
    createSampleImage("../uploads/gambling/sample1.jpg", 400, 300, "#e74c3c", "Cờ Bạc Truyền Thống");
    createSampleImage("../uploads/gambling/sample2.jpg", 400, 300, "#3498db", "Cờ Bạc Trực Tuyến");
    createSampleImage("../uploads/gambling/sample3.jpg", 400, 300, "#2ecc71", "Game Có Yếu Tố Cờ Bạc");
    createSampleImage("../uploads/gambling/sample4.jpg", 400, 300, "#f39c12", "Đầu Tư Rủi Ro Cao");
    
    // Tạo ảnh mẫu cho chi tiết cờ bạc
    createSampleImage("../uploads/gambling/detail1.jpg", 300, 300, "#c0392b", "Đánh bài");
    createSampleImage("../uploads/gambling/detail2.jpg", 300, 300, "#8e44ad", "Casino trực tuyến");
    
    // Tạo ảnh mẫu cho avatar
    createSampleImage("../uploads/gambling/avatars/avatar1.jpg", 150, 150, "#34495e", "Hoàng M.");
    createSampleImage("../uploads/gambling/avatars/avatar2.jpg", 150, 150, "#16a085", "Thu H.");
    
    echo "Đã tạo các hình ảnh mẫu thành công.<br>";
} catch (Exception $e) {
    die("Lỗi khi tạo hình ảnh mẫu: " . $e->getMessage());
}

echo "<strong>Hoàn thành việc điền dữ liệu mẫu!</strong><br>";
echo "<a href='../admin/gambling_manage.php'>Đi đến trang quản trị</a>";
?> 