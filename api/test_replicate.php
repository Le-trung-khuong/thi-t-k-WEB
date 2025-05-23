<?php
/**
 * Test Replicate API Connection
 * File này dùng để kiểm tra kết nối đến Replicate API
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

// API key (trong môi trường thực tế, nên lưu trong biến môi trường)
$replicate_api_key = 'r8_ZVM5uzlNhyWTnRMWeB2U7aYSvHNnVsK0WwnKi'; // Thay thế bằng API key thực của bạn

// Hàm gửi phản hồi lỗi
function sendResponse($success, $message, $data = [], $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Only POST method is allowed', [], 405);
}

// Lấy dữ liệu từ request
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['action']) || $input['action'] !== 'test_connection') {
    sendResponse(false, 'Invalid action', [], 400);
}

// Gọi Replicate API để kiểm tra kết nối
$ch = curl_init('https://api.replicate.com/v1/models');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Token ' . $replicate_api_key
]);

$response = curl_exec($ch);
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Kiểm tra lỗi trong request
if ($curl_error) {
    sendResponse(false, 'Curl error: ' . $curl_error);
}

if ($status_code !== 200) {
    sendResponse(false, 'API error: ' . $response, ['status_code' => $status_code]);
}

// Phân tích dữ liệu
$result = json_decode($response, true);
if (!isset($result['results']) || !is_array($result['results'])) {
    sendResponse(false, 'Invalid API response format');
}

// API hoạt động, trả về thành công
sendResponse(true, 'Replicate API connection successful', [
    'models_count' => count($result['results']),
    'api_version' => $result['next'] ? 'v1' : 'unknown'
]); 