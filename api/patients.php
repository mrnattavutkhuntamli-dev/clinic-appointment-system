<?php
/**
 * api/patients.php
 * AJAX API สำหรับจัดการผู้ป่วย (CRUD)
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/phone.php';
require_once __DIR__ . '/../helpers/hn.php';

// ============================================
// Helper: ส่ง JSON response
// ============================================
function respond($success, $data = null, $error = null)
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

// ============================================
// รับ action จาก request
// ============================================
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    $input  = $_GET;
} else {
    $input  = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';
}

// ============================================
// Route
// ============================================
try {
    switch ($action) {
        case 'list':
            actionList($pdo);
            break;
        case 'search':
            actionSearch($pdo, $input);
            break;
        case 'get':
            actionGet($pdo, $input);
            break;
        case 'create':
            actionCreate($pdo, $input);
            break;
        case 'update':
            actionUpdate($pdo, $input);
            break;
        case 'delete':
            actionDelete($pdo, $input);
            break;
        default:
            respond(false, null, 'Invalid action');
    }
} catch (PDOException $e) {
    error_log('API patients error: ' . $e->getMessage());
    respond(false, null, 'เกิดข้อผิดพลาดในระบบ');
} catch (Exception $e) {
      error_log('API patients error: ' . $e->getMessage());
    respond(false, null, 'DB Error: ' . $e->getMessage());  // ← โชว์ error จริง
}

// ============================================
// Actions
// ============================================

function actionList($pdo)
{
    $stmt = $pdo->query("
        SELECT id, hn, first_name, last_name, phone, birth_date, registered_at
        FROM tb_patients
        ORDER BY hn ASC
        LIMIT 500
    ");
    $patients = $stmt->fetchAll();
    
    respond(true, $patients);
}

function actionSearch($pdo, $input)
{
    $q = trim($input['q'] ?? '');
    
    if ($q === '') {
        actionList($pdo);
        return;
    }
    
    // เตรียมค่าไว้ล่วงหน้า
    $likeQ      = "%$q%";
    $phoneDigits = preg_replace('/\D/', '', $q);
    $likePhone  = "%$phoneDigits%";
    
    $sql = "
        SELECT id, hn, first_name, last_name, phone, birth_date, registered_at
        FROM tb_patients
        WHERE hn LIKE :q1
           OR CONCAT(first_name, ' ', last_name) LIKE :q2
           OR first_name LIKE :q3
           OR last_name LIKE :q4
           OR phone LIKE :q5
        ORDER BY hn ASC
        LIMIT 100
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':q1' => $likeQ,
        ':q2' => $likeQ,
        ':q3' => $likeQ,
        ':q4' => $likeQ,
        ':q5' => $likePhone,
    ]);
    
    respond(true, $stmt->fetchAll());
}
function actionGet($pdo, $input)
{
    $id = (int)($input['id'] ?? 0);
    
    if ($id <= 0) {
        respond(false, null, 'Invalid ID');
    }
    
    $stmt = $pdo->prepare("
        SELECT id, hn, first_name, last_name, phone, birth_date, registered_at
        FROM tb_patients
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $patient = $stmt->fetch();
    
    if (!$patient) {
        respond(false, null, 'ไม่พบผู้ป่วย');
    }
    
    respond(true, $patient);
}

function actionCreate($pdo, $input)
{
    // ===== Validate =====
    $first_name = trim($input['first_name'] ?? '');
    $last_name  = trim($input['last_name']  ?? '');
    $phone_raw  = trim($input['phone']      ?? '');
    $birth_date = trim($input['birth_date'] ?? '');
    
    if ($first_name === '') {
        respond(false, null, 'กรุณากรอกชื่อ');
    }
    if ($last_name === '') {
        respond(false, null, 'กรุณากรอกนามสกุล');
    }
    
    // Normalize เบอร์โทร
    $phone = normalizePhone($phone_raw);
    
    if ($phone === false) {
        respond(false, null, 'เบอร์โทรไม่ถูกต้อง (ต้องเป็น 10 หลัก)');
    }
    
    // ตรวจสอบวันเกิด
    if ($birth_date === '' || !strtotime($birth_date)) {
        respond(false, null, 'กรุณากรอกวันเกิดให้ถูกต้อง');
    }
    
    // ===== Generate HN =====
    $hn = generateHN($pdo);
    
    // ===== Insert =====
    $stmt = $pdo->prepare("
        INSERT INTO tb_patients (hn, first_name, last_name, phone, birth_date, registered_at)
        VALUES (:hn, :first_name, :last_name, :phone, :birth_date, CURDATE())
    ");
    $stmt->execute([
        ':hn'         => $hn,
        ':first_name' => $first_name,
        ':last_name'  => $last_name,
        ':phone'      => $phone,
        ':birth_date' => $birth_date,
    ]);
    
    $id = $pdo->lastInsertId();
    
    respond(true, [
        'id'         => (int)$id,
        'hn'         => $hn,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'phone'      => $phone,
        'birth_date' => $birth_date,
    ]);
}

function actionUpdate($pdo, $input)
{
    $id = (int)($input['id'] ?? 0);
    
    if ($id <= 0) {
        respond(false, null, 'Invalid ID');
    }
    
    // ตรวจสอบว่ามีผู้ป่วยคนนี้
    $stmt = $pdo->prepare("SELECT id, hn FROM tb_patients WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    
    if (!$existing) {
        respond(false, null, 'ไม่พบผู้ป่วย');
    }
    
    // ===== Validate =====
    $first_name = trim($input['first_name'] ?? '');
    $last_name  = trim($input['last_name']  ?? '');
    $phone_raw  = trim($input['phone']      ?? '');
    $birth_date = trim($input['birth_date'] ?? '');
    
    if ($first_name === '' || $last_name === '') {
        respond(false, null, 'กรุณากรอกชื่อ-นามสกุล');
    }
    
    $phone = normalizePhone($phone_raw);
    if ($phone === false) {
        respond(false, null, 'เบอร์โทรไม่ถูกต้อง');
    }
    
    if ($birth_date === '' || !strtotime($birth_date)) {
        respond(false, null, 'กรุณากรอกวันเกิดให้ถูกต้อง');
    }
    
    // ===== Update =====
    $stmt = $pdo->prepare("
        UPDATE tb_patients
        SET first_name = :first_name,
            last_name  = :last_name,
            phone      = :phone,
            birth_date = :birth_date
        WHERE id = :id
    ");
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name'  => $last_name,
        ':phone'      => $phone,
        ':birth_date' => $birth_date,
        ':id'         => $id,
    ]);
    
    respond(true, ['id' => $id]);
}

function actionDelete($pdo, $input)
{
    $id = (int)($input['id'] ?? 0);
    
    if ($id <= 0) {
        respond(false, null, 'Invalid ID');
    }
    
    // ตรวจสอบว่ามีนัดที่ยัง active อยู่ไหม
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM tb_appointments 
        WHERE patient_id = ? AND status != 'cancelled'
    ");
    $stmt->execute([$id]);
    $activeCount = (int)$stmt->fetchColumn();
    
    if ($activeCount > 0) {
        respond(false, null, "ไม่สามารถลบได้ มีนัดที่ยังใช้งานอยู่ $activeCount รายการ");
    }
    
    // ลบ (CASCADE จะลบนัดที่ cancelled ให้)
    $stmt = $pdo->prepare("DELETE FROM tb_patients WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        respond(false, null, 'ไม่พบผู้ป่วย');
    }
    
    respond(true);
}