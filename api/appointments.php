<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/response.php';

// ============================================
// Helper: ส่ง JSON response
// ============================================
setJsonHeaders();
// ============================================
// รับ action จาก request
// ============================================
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $action = $_GET['action'] ?? 'list';
    $input = $_GET;
} else {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';
}

try {
    switch ($action) {
        case 'list':
            actionList($pdo, $input);
            break;
        case 'get':
            actionGet($pdo, $input);
            break;
        case 'create':
            actionCreate($pdo, $input);
            break;
        case 'update_status':
            actionUpdateStatus($pdo, $input);
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

function actionList($pdo, $input)
{
    // รับค่า input date
    $date = $input['date'] ?? date('Y-m-d');
    
    // ตรวจสอบรูปแบบวันที่
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        respond(false, null, 'รูปแบบวันที่ไม่ถูกต้อง');
    }
    
    $sql = "
        SELECT 
            a.id,
            a.patient_id,
            a.doctor_id,
            a.start_time,
            a.end_time,
            a.status,
            p.hn,
            p.first_name,
            p.last_name,
            p.phone,
            d.name AS doctor_name
        FROM tb_appointments a
        JOIN tb_patients p ON a.patient_id = p.id
        JOIN tb_doctors d  ON a.doctor_id  = d.id
        WHERE DATE(a.start_time) = :date
        ORDER BY a.start_time ASC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':date' => $date]);
    $appointments = $stmt->fetchAll();
    
    respond(true, $appointments);
}

function actionGet($pdo, $input)
{
    $id = (int)($input['id'] ?? 0);
    if ($id <= 0) {
        respond(false, null, 'Invalid ID');
    }
    
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            p.hn, p.first_name, p.last_name,
            d.name AS doctor_name
        FROM tb_appointments a
        JOIN tb_patients p ON a.patient_id = p.id
        JOIN tb_doctors d  ON a.doctor_id  = d.id
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $appt = $stmt->fetch();
    
    if (!$appt) {
        respond(false, null, 'ไม่พบนัดหมาย');
    }
    
    respond(true, $appt);
}


function actionCreate($pdo, $input)
{
    // ===== รับค่า =====
    $patient_id = (int)($input['patient_id'] ?? 0);
    $doctor_id  = (int)($input['doctor_id']  ?? 0);
    $date       = trim($input['date']       ?? '');
    $start_time = trim($input['start_time'] ?? '');
    $end_time   = trim($input['end_time']   ?? '');
    
    // ===== Validate =====
    if ($patient_id <= 0) {
        respond(false, null, 'กรุณาเลือกผู้ป่วย');
    }
    if ($doctor_id <= 0) {
        respond(false, null, 'กรุณาเลือกแพทย์');
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        respond(false, null, 'กรุณาเลือกวันที่');
    }
    if (!preg_match('/^\d{2}:\d{2}$/', $start_time) || !preg_match('/^\d{2}:\d{2}$/', $end_time)) {
        respond(false, null, 'กรุณาระบุเวลาให้ถูกต้อง');
    }
    
    // ===== ประกอบ datetime =====
    $start_dt = "$date $start_time:00";
    $end_dt   = "$date $end_time:00";
    
    // ตรวจสอบเวลาเริ่ม < เวลาจบ
    if (strtotime($start_dt) >= strtotime($end_dt)) {
        respond(false, null, 'เวลาเริ่มต้องก่อนเวลาสิ้นสุด');
    }
    
    // ===== ตรวจสอบว่าผู้ป่วยมีอยู่จริง =====
    $stmt = $pdo->prepare("SELECT id, hn FROM tb_patients WHERE id = ?");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch();
    if (!$patient) {
        respond(false, null, 'ไม่พบผู้ป่วย');
    }
    
    // ===== ตรวจสอบว่าแพทย์มีอยู่จริง =====
    $stmt = $pdo->prepare("SELECT id, name FROM tb_doctors WHERE id = ?");
    $stmt->execute([$doctor_id]);
    $doctor = $stmt->fetch();
    if (!$doctor) {
        respond(false, null, 'ไม่พบแพทย์');
    }
    
    // ===== ตรวจเวลาชน (หัวใจสำคัญ!) =====
    // นัดใหม่ชนกับนัดเดิม เมื่อ:
    //   new.start < old.end AND new.end > old.start
    // (ไม่นับนัดที่ cancelled)
    $stmt = $pdo->prepare("
        SELECT id, start_time, end_time, 
               CONCAT(DATE_FORMAT(start_time, '%H:%i'), ' - ', DATE_FORMAT(end_time, '%H:%i')) AS time_range
        FROM tb_appointments
        WHERE doctor_id = :doctor_id
          AND status != 'cancelled'
          AND start_time < :new_end
          AND end_time > :new_start
        LIMIT 1
    ");
    $stmt->execute([
        ':doctor_id' => $doctor_id,
        ':new_start' => $start_dt,
        ':new_end'   => $end_dt,
    ]);
    $conflict = $stmt->fetch();
    
    if ($conflict) {
        respond(false, null, "แพทย์มีนัดในช่วงเวลานี้แล้ว ({$conflict['time_range']})");
    }
    
    // ===== Insert =====
    $stmt = $pdo->prepare("
        INSERT INTO tb_appointments (patient_id, doctor_id, start_time, end_time, status)
        VALUES (:patient_id, :doctor_id, :start_time, :end_time, 'pending')
    ");
    $stmt->execute([
        ':patient_id' => $patient_id,
        ':doctor_id'  => $doctor_id,
        ':start_time' => $start_dt,
        ':end_time'   => $end_dt,
    ]);
    
    $newId = $pdo->lastInsertId();
    
    respond(true, [
        'id'          => (int)$newId,
        'patient_hn'  => $patient['hn'],
        'doctor_name' => $doctor['name'],
    ]);
}


function actionUpdateStatus($pdo, $input)
{
    $id     = (int)($input['id'] ?? 0);
    $status = $input['status'] ?? '';
    
    if ($id <= 0) {
        respond(false, null, 'Invalid ID');
    }
    
    $allowed = ['pending', 'arrived', 'cancelled'];
    if (!in_array($status, $allowed, true)) {
        respond(false, null, 'สถานะไม่ถูกต้อง');
    }
    
    $stmt = $pdo->prepare("UPDATE tb_appointments SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    if ($stmt->rowCount() === 0) {
        respond(false, null, 'ไม่พบนัดหมาย');
    }
    
    respond(true, ['id' => $id, 'status' => $status]);
}