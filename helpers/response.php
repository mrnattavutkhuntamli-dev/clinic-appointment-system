<?php
function respond(bool $success, $data = null, ?string $error = null): void
{
    $response = ['success' => $success];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    if ($error !== null) {
        $response['error'] = $error;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * ตั้ง header สำหรับ JSON response
 * เรียกใช้ต้นไฟล์ API ทุกไฟล์
 */
function setJsonHeaders(): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
}