<?php
/**
 * API phục vụ mô hình face-api từ server thay vì tải từ CDN
 * Điều này sẽ giúp tránh lỗi "Failed to fetch" từ CDN
 */

// Thiết lập header để cho phép CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Đường dẫn gốc đến thư mục chứa các mô hình
$modelBasePath = '../models/face-api-models';

// Lấy tên mô hình từ tham số truy vấn
$modelName = isset($_GET['model']) ? $_GET['model'] : '';

// Danh sách mô hình được phép
$allowedModels = [
    'tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1',
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2',
    'ssd_mobilenetv1_model-weights_manifest.json',
    'ssd_mobilenetv1_model-shard1',
    'ssd_mobilenetv1_model-shard2'
];

// Kiểm tra nếu tên mô hình không được cung cấp hoặc không hợp lệ
if (empty($modelName) || !in_array($modelName, $allowedModels)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Model name is invalid or not specified']);
    exit;
}

// Đường dẫn đầy đủ đến tệp mô hình
$modelPath = $modelBasePath . '/' . $modelName;

// Kiểm tra nếu tệp không tồn tại
if (!file_exists($modelPath)) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Model file not found']);
    exit;
}

// Xác định loại MIME
$extension = pathinfo($modelName, PATHINFO_EXTENSION);
if ($extension === 'json') {
    header('Content-Type: application/json');
} else {
    header('Content-Type: application/octet-stream');
}

// Đọc và gửi nội dung tệp
readfile($modelPath);
exit; 