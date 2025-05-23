<?php
/**
 * Script tự động tải mô hình face-api.js nếu chưa tồn tại
 */

// Thư mục lưu mô hình
$outputDir = '../models/face-api-models';

// Đảm bảo thư mục tồn tại
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "Đã tạo thư mục $outputDir\n";
}

// Danh sách mô hình cần tải
$models = [
    [
        'name' => 'tiny_face_detector_model-weights_manifest.json',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/tiny_face_detector_model-weights_manifest.json'
    ],
    [
        'name' => 'tiny_face_detector_model-shard1',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/tiny_face_detector_model-shard1'
    ],
    [
        'name' => 'face_landmark_68_model-weights_manifest.json',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_landmark_68_model-weights_manifest.json'
    ],
    [
        'name' => 'face_landmark_68_model-shard1',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_landmark_68_model-shard1'
    ],
    [
        'name' => 'face_recognition_model-weights_manifest.json',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_recognition_model-weights_manifest.json'
    ],
    [
        'name' => 'face_recognition_model-shard1',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_recognition_model-shard1'
    ],
    [
        'name' => 'face_recognition_model-shard2',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/face_recognition_model-shard2'
    ],
    [
        'name' => 'ssd_mobilenetv1_model-weights_manifest.json',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/ssd_mobilenetv1_model-weights_manifest.json'
    ],
    [
        'name' => 'ssd_mobilenetv1_model-shard1',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/ssd_mobilenetv1_model-shard1'
    ],
    [
        'name' => 'ssd_mobilenetv1_model-shard2',
        'url' => 'https://github.com/justadudewhohacks/face-api.js/raw/master/weights/ssd_mobilenetv1_model-shard2'
    ]
];

// Hàm tải một file
function downloadFile($url, $outputPath) {
    echo "Đang tải $url...\n";
    
    // Thiết lập ngữ cảnh stream
    $context = stream_context_create([
        'http' => [
            'timeout' => 60,  // Thời gian chờ 60 giây
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
        ]
    ]);
    
    // Tải file
    $fileContent = @file_get_contents($url, false, $context);
    
    if ($fileContent === false) {
        echo "Lỗi: Không thể tải $url\n";
        return false;
    }
    
    // Lưu nội dung vào file
    if (file_put_contents($outputPath, $fileContent) === false) {
        echo "Lỗi: Không thể lưu file $outputPath\n";
        return false;
    }
    
    echo "Đã tải thành công: $outputPath\n";
    return true;
}

// Tạo hàm kiểm tra xem file đã tồn tại chưa
function checkExistingModels() {
    global $models, $outputDir;
    $missingModels = [];
    
    foreach ($models as $model) {
        $outputPath = $outputDir . '/' . $model['name'];
        if (!file_exists($outputPath)) {
            $missingModels[] = $model;
        }
    }
    
    return $missingModels;
}

// Chỉ tải các mô hình còn thiếu
$missingModels = checkExistingModels();

if (empty($missingModels)) {
    echo "Tất cả mô hình đã tồn tại!\n";
} else {
    echo "Đang tải " . count($missingModels) . " mô hình còn thiếu...\n";
    
    foreach ($missingModels as $model) {
        $outputPath = $outputDir . '/' . $model['name'];
        downloadFile($model['url'], $outputPath);
    }
    
    echo "Hoàn tất tải mô hình!\n";
}

// Cập nhật file manifest để điều chỉnh đường dẫn
foreach ($models as $model) {
    if (strpos($model['name'], 'manifest.json') !== false) {
        $manifestPath = $outputDir . '/' . $model['name'];
        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            
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
                file_put_contents($manifestPath, json_encode($manifest));
                echo "Đã cập nhật file manifest: $manifestPath\n";
            }
        }
    }
}

echo "Quá trình hoàn tất!\n"; 