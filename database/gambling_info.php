<?php
// Kết nối đến cơ sở dữ liệu
require_once "config.php";

// Thiết lập header cho API
header('Content-Type: application/json; charset=utf-8');

// Lấy action từ request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Xử lý các action
switch ($action) {
    case 'types':
        // Lấy tất cả các loại cờ bạc
        getGamblingTypes();
        break;
    
    case 'details':
        // Lấy chi tiết về một loại cờ bạc
        $name = isset($_GET['name']) ? $_GET['name'] : '';
        getGamblingDetails($name);
        break;
    
    case 'stories':
        // Lấy câu chuyện về cờ bạc
        getGamblingStories();
        break;
    
    case 'add_type':
        // Thêm loại cờ bạc mới (chỉ cho admin)
        addGamblingType();
        break;
    
    case 'add_detail':
        // Thêm chi tiết cờ bạc mới (chỉ cho admin)
        addGamblingDetail();
        break;
    
    case 'add_story':
        // Thêm câu chuyện cờ bạc mới (chỉ cho admin)
        addGamblingStory();
        break;
    
    default:
        // Action không hợp lệ
        echo json_encode([
            'success' => false,
            'message' => 'Hành động không hợp lệ'
        ]);
        break;
}

// Hàm lấy tất cả các loại cờ bạc
function getGamblingTypes() {
    global $conn;
    
    try {
        // Lấy danh sách các loại cờ bạc
        $stmt = $conn->prepare("SELECT * FROM gambling_types");
        $stmt->execute();
        $types = $stmt->fetchAll();
        
        // Lấy các rủi ro và ví dụ cho từng loại
        foreach ($types as &$type) {
            // Lấy rủi ro
            $stmt = $conn->prepare("SELECT risk FROM gambling_risks WHERE gambling_type_id = :id");
            $stmt->bindParam(':id', $type['id']);
            $stmt->execute();
            $risks = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $type['risks'] = $risks;
            
            // Lấy ví dụ
            $stmt = $conn->prepare("SELECT name FROM gambling_examples WHERE gambling_type_id = :id");
            $stmt->bindParam(':id', $type['id']);
            $stmt->execute();
            $examples = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $type['examples'] = $examples;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $types
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm lấy chi tiết về một loại cờ bạc
function getGamblingDetails($name) {
    global $conn;
    
    try {
        // Tìm kiếm cờ bạc theo tên hoặc loại
        $stmt = $conn->prepare("SELECT * FROM gambling_details WHERE name LIKE :name OR type LIKE :name");
        $search = "%$name%";
        $stmt->bindParam(':name', $search);
        $stmt->execute();
        $details = $stmt->fetchAll();
        
        if (count($details) > 0) {
            foreach ($details as &$detail) {
                // Lấy tác động ngắn hạn
                $stmt = $conn->prepare("SELECT description FROM gambling_impacts WHERE gambling_id = :id AND is_long_term = 0");
                $stmt->bindParam(':id', $detail['id']);
                $stmt->execute();
                $shortTerm = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $detail['shortTerm'] = $shortTerm;
                
                // Lấy tác động dài hạn
                $stmt = $conn->prepare("SELECT description FROM gambling_impacts WHERE gambling_id = :id AND is_long_term = 1");
                $stmt->bindParam(':id', $detail['id']);
                $stmt->execute();
                $longTerm = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $detail['longTerm'] = $longTerm;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $details
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy thông tin về cờ bạc này'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm lấy câu chuyện về cờ bạc
function getGamblingStories() {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT * FROM gambling_stories ORDER BY id DESC");
        $stmt->execute();
        $stories = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => $stories
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi: ' . $e->getMessage()
        ]);
    }
}

// Hàm thêm loại cờ bạc mới (yêu cầu xác thực admin)
function addGamblingType() {
    global $conn;
    
    // Kiểm tra nếu là phương thức POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy dữ liệu từ request
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $risks = isset($_POST['risks']) ? $_POST['risks'] : '';
        $examples = isset($_POST['examples']) ? $_POST['examples'] : '';
        
        // Kiểm tra dữ liệu
        if (empty($type) || empty($description)) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin bắt buộc'
            ]);
            return;
        }
        
        // Xử lý upload ảnh
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = uploadImage($_FILES['image'], 'uploads/gambling');
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu hình ảnh'
            ]);
            return;
        }
        
        try {
            // Bắt đầu transaction
            $conn->beginTransaction();
            
            // Thêm loại cờ bạc
            $stmt = $conn->prepare("INSERT INTO gambling_types (type, description, image) VALUES (:type, :description, :image)");
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image', $image);
            $stmt->execute();
            
            // Lấy ID của loại cờ bạc vừa thêm
            $typeId = $conn->lastInsertId();
            
            // Thêm các rủi ro
            if (!empty($risks)) {
                $risksArr = explode('|', $risks);
                foreach ($risksArr as $risk) {
                    $stmt = $conn->prepare("INSERT INTO gambling_risks (gambling_type_id, risk) VALUES (:type_id, :risk)");
                    $stmt->bindParam(':type_id', $typeId);
                    $stmt->bindParam(':risk', $risk);
                    $stmt->execute();
                }
            }
            
            // Thêm các ví dụ
            if (!empty($examples)) {
                $examplesArr = explode('|', $examples);
                foreach ($examplesArr as $example) {
                    $stmt = $conn->prepare("INSERT INTO gambling_examples (gambling_type_id, name) VALUES (:type_id, :name)");
                    $stmt->bindParam(':type_id', $typeId);
                    $stmt->bindParam(':name', $example);
                    $stmt->execute();
                }
            }
            
            // Hoàn thành transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Thêm loại cờ bạc thành công',
                'id' => $typeId
            ]);
        } catch (PDOException $e) {
            // Rollback nếu có lỗi
            $conn->rollback();
            
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức không hợp lệ'
        ]);
    }
}

// Hàm thêm chi tiết cờ bạc
function addGamblingDetail() {
    global $conn;
    
    // Kiểm tra nếu là phương thức POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy dữ liệu từ request
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $dangerLevel = isset($_POST['danger_level']) ? $_POST['danger_level'] : 'medium';
        $shortTerm = isset($_POST['short_term']) ? $_POST['short_term'] : '';
        $longTerm = isset($_POST['long_term']) ? $_POST['long_term'] : '';
        
        // Kiểm tra dữ liệu
        if (empty($name) || empty($type) || empty($description)) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin bắt buộc'
            ]);
            return;
        }
        
        // Xử lý upload ảnh
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = uploadImage($_FILES['image'], 'uploads/gambling');
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu hình ảnh'
            ]);
            return;
        }
        
        try {
            // Bắt đầu transaction
            $conn->beginTransaction();
            
            // Thêm chi tiết cờ bạc
            $stmt = $conn->prepare("INSERT INTO gambling_details (name, type, description, image, danger_level) VALUES (:name, :type, :description, :image, :danger_level)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':danger_level', $dangerLevel);
            $stmt->execute();
            
            // Lấy ID của chi tiết vừa thêm
            $detailId = $conn->lastInsertId();
            
            // Thêm tác động ngắn hạn
            if (!empty($shortTerm)) {
                $shortTermArr = explode('|', $shortTerm);
                foreach ($shortTermArr as $effect) {
                    $stmt = $conn->prepare("INSERT INTO gambling_impacts (gambling_id, description, is_long_term) VALUES (:id, :description, 0)");
                    $stmt->bindParam(':id', $detailId);
                    $stmt->bindParam(':description', $effect);
                    $stmt->execute();
                }
            }
            
            // Thêm tác động dài hạn
            if (!empty($longTerm)) {
                $longTermArr = explode('|', $longTerm);
                foreach ($longTermArr as $effect) {
                    $stmt = $conn->prepare("INSERT INTO gambling_impacts (gambling_id, description, is_long_term) VALUES (:id, :description, 1)");
                    $stmt->bindParam(':id', $detailId);
                    $stmt->bindParam(':description', $effect);
                    $stmt->execute();
                }
            }
            
            // Hoàn thành transaction
            $conn->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Thêm chi tiết cờ bạc thành công',
                'id' => $detailId
            ]);
        } catch (PDOException $e) {
            // Rollback nếu có lỗi
            $conn->rollback();
            
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức không hợp lệ'
        ]);
    }
}

// Hàm thêm câu chuyện cờ bạc
function addGamblingStory() {
    global $conn;
    
    // Kiểm tra nếu là phương thức POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy dữ liệu từ request
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $age = isset($_POST['age']) ? intval($_POST['age']) : 0;
        $story = isset($_POST['story']) ? $_POST['story'] : '';
        
        // Kiểm tra dữ liệu
        if (empty($name) || $age <= 0 || empty($story)) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu thông tin bắt buộc'
            ]);
            return;
        }
        
        // Xử lý upload ảnh avatar
        $avatar = '';
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar = uploadImage($_FILES['avatar'], 'uploads/gambling/avatars');
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu ảnh đại diện'
            ]);
            return;
        }
        
        try {
            // Thêm câu chuyện
            $stmt = $conn->prepare("INSERT INTO gambling_stories (name, age, avatar, story) VALUES (:name, :age, :avatar, :story)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':age', $age);
            $stmt->bindParam(':avatar', $avatar);
            $stmt->bindParam(':story', $story);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'message' => 'Thêm câu chuyện thành công',
                'id' => $conn->lastInsertId()
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Phương thức không hợp lệ'
        ]);
    }
}
?> 