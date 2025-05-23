<?php
/**
 * Local proxy để tải mô hình face-api.js trực tiếp
 * Trang này cho phép client tải mô hình trực tiếp qua browser
 */

// Thiết lập header để cho phép CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Lấy tên mô hình từ tham số truy vấn
$modelName = isset($_GET['model']) ? $_GET['model'] : '';

// Thư mục đích để lưu tệp
$outputDir = '../models/face-api-models';

// Danh sách mô hình được phép
$allowedModels = [
    'tiny_face_detector_model-weights_manifest.json' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/tiny_face_detector_model-shard1',
    'face_landmark_68_model-weights_manifest.json' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_recognition_model-shard1',
    'face_recognition_model-shard2' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_recognition_model-shard2',
    'ssd_mobilenetv1_model-weights_manifest.json' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/ssd_mobilenetv1_model-weights_manifest.json',
    'ssd_mobilenetv1_model-shard1' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/ssd_mobilenetv1_model-shard1',
    'ssd_mobilenetv1_model-shard2' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/ssd_mobilenetv1_model-shard2'
];

// Kiểm tra nếu tên mô hình không được cung cấp hoặc không hợp lệ
if (empty($modelName) || !isset($allowedModels[$modelName])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Model name is invalid or not specified']);
    exit;
}

// Đường dẫn file đích
$outputPath = $outputDir . '/' . $modelName;

// Đảm bảo thư mục tồn tại
if (!file_exists($outputDir)) {
    if (!mkdir($outputDir, 0755, true)) {
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Không thể tạo thư mục']);
        exit;
    }
}

// URL của mô hình
$modelUrl = $allowedModels[$modelName];

// Kiểm tra nếu tệp đã tồn tại
if (file_exists($outputPath) && filesize($outputPath) > 0) {
    // Nếu tệp đã tồn tại, phục vụ tệp trực tiếp
    $extension = pathinfo($modelName, PATHINFO_EXTENSION);
    if ($extension === 'json') {
        header('Content-Type: application/json');
    } else {
        header('Content-Type: application/octet-stream');
    }
    readfile($outputPath);
    exit;
}

// Tải tệp từ URL
$context = stream_context_create([
    'http' => [
        'timeout' => 60,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ]
]);

$fileContent = @file_get_contents($modelUrl, false, $context);

if ($fileContent === false) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Không thể tải mô hình từ GitHub']);
    exit;
}

// Lưu tệp vào thư mục cục bộ
if (file_put_contents($outputPath, $fileContent) === false) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Không thể lưu mô hình vào hệ thống tệp']);
    exit;
}

// Nếu là tệp manifest.json, cập nhật đường dẫn
if (strpos($modelName, 'manifest.json') !== false) {
    $manifest = json_decode($fileContent, true);
    
    if (is_array($manifest)) {
        foreach ($manifest as &$entry) {
            if (isset($entry['weightsManifest']) && is_array($entry['weightsManifest'])) {
                foreach ($entry['weightsManifest'] as &$weightsEntry) {
                    if (isset($weightsEntry['paths']) && is_array($weightsEntry['paths'])) {
                        // Cập nhật đường dẫn để sử dụng API serve-models.php
                        foreach ($weightsEntry['paths'] as &$path) {
                            // Lấy tên file từ đường dẫn
                            $filename = basename($path);
                            // Cập nhật đường dẫn để dùng API
                            $path = $filename;
                        }
                    }
                }
            }
        }
        
        // Lưu manifest đã cập nhật
        file_put_contents($outputPath, json_encode($manifest));
        $fileContent = json_encode($manifest);
    }
}

// Phục vụ tệp
$extension = pathinfo($modelName, PATHINFO_EXTENSION);
if ($extension === 'json') {
    header('Content-Type: application/json');
} else {
    header('Content-Type: application/octet-stream');
}

// Gửi dữ liệu
echo $fileContent;
exit; 