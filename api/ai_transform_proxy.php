<?php
/**
 * AI Transform Proxy
 * Proxy script để gọi Replicate API từ client-side code
 * Giúp ẩn API key và tránh vấn đề CORS
 */

// Cấu hình
header('Content-Type: application/json');
// Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// API key (trong môi trường thực tế, nên lưu trong biến môi trường)
$replicate_api_key = 'r8_ZVM5uzlNhyWTnRMWeB2U7aYSvHNnVsK0WwnKi'; // Thay thế bằng API key thực của bạn

// Mô hình Stable Diffusion Image-to-Image
$model_version = "35d298c872406606a5721735e2c796012cfaef9f91ab3f76b018c8b16fa2da75";

// Hàm gửi phản hồi lỗi
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError('Only POST method is allowed', 405);
}

// Kiểm tra tham số bắt buộc
if (!isset($_POST['image']) || !isset($_POST['prompt'])) {
    sendError('Missing required parameters: image and prompt');
}

// Lấy dữ liệu từ request
$image_data = $_POST['image'];
$prompt = $_POST['prompt'];
$effect_type = $_POST['effect_type'] ?? 'general';
$years = intval($_POST['years'] ?? 5);

// Tạo negative prompt phù hợp với use case
$negative_prompt = "cartoon, anime, drawing, painting, distorted, blurry, low quality, bad quality, deformed";

// Tham số xử lý
$inference_steps = 30;
$guidance_scale = 7.5;
$strength = 0.75; // Mức độ biến đổi: 0.5-0.8 là tốt nhất để giữ nguyên đặc điểm nhận dạng

// Tạo payload cho Replicate API
$payload = [
    'version' => $model_version,
    'input' => [
        'image' => $image_data,
        'prompt' => $prompt,
        'negative_prompt' => $negative_prompt,
        'num_inference_steps' => $inference_steps,
        'guidance_scale' => $guidance_scale,
        'strength' => $strength
    ]
];

// Gọi Replicate API để tạo prediction
$ch = curl_init('https://api.replicate.com/v1/predictions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Token ' . $replicate_api_key
]);

$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Kiểm tra lỗi trong request
if ($curl_error) {
    sendError('Curl error: ' . $curl_error);
}

if ($status_code !== 201) {
    sendError('API error: ' . $response, $status_code);
}

// Parse kết quả
$prediction = json_decode($response, true);
if (!isset($prediction['urls']['get'])) {
    sendError('Invalid API response: ' . $response);
}

// Lấy URL để poll kết quả
$get_url = $prediction['urls']['get'];
$prediction_id = $prediction['id'];

// Poll kết quả (đợi cho đến khi xử lý hoàn tất)
$max_attempts = 30;
$attempts = 0;
$result = null;

while ($attempts < $max_attempts) {
    // Đợi 2 giây giữa các lần kiểm tra
    sleep(2);
    
    // Gửi request kiểm tra trạng thái
    $ch = curl_init($get_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Token ' . $replicate_api_key
    ]);
    
    $status_response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($status_code !== 200) {
        sendError('Error checking prediction status: ' . $status_response);
    }
    
    $result = json_decode($status_response, true);
    
    // Kiểm tra trạng thái
    if ($result['status'] === 'succeeded') {
        break; // Xử lý thành công
    } elseif ($result['status'] === 'failed') {
        sendError('Prediction failed: ' . ($result['error'] ?? 'Unknown error'));
    }
    
    $attempts++;
}

// Kiểm tra quá thời gian
if ($attempts >= $max_attempts) {
    sendError('Timed out waiting for prediction result');
}

// Kiểm tra output
if (!isset($result['output']) || !is_array($result['output']) || empty($result['output'])) {
    sendError('No output received from prediction');
}

// Trả về URL hình ảnh biến đổi
echo json_encode([
    'output_url' => $result['output'][0],
    'prediction_id' => $prediction_id,
    'status' => 'success'
]); 