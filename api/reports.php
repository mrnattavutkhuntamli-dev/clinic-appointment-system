<?php
/**
 * api/reports.php
 * AJAX API สำหรับรายงานสรุป
 * 
 * Actions:
 *   GET ?action=summary&from=2026-10-01&to=2026-10-31
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/response.php';

setJsonHeaders();

// ===== รับ action =====
$method = $_SERVER['REQUEST_METHOD'];
$action = ($method === 'GET') ? ($_GET['action'] ?? 'summary') : '';
$input  = $_GET;

// ===== Route =====
try {
    switch ($action) {
        case 'summary':
            actionSummary($pdo, $input);
            break;
        default:
            respond(false, null, 'Invalid action');
    }
} catch (PDOException $e) {
    error_log('API reports error: ' . $e->getMessage());
    respond(false, null, 'เกิดข้อผิดพลาดในระบบ');
} catch (Exception $e) {
    respond(false, null, $e->getMessage());
}

// ============================================================
// Actions
// ============================================================

/**
 * สรุปรายงานตามช่วงวันที่
 * 
 * ใช้ Aggregate Functions ทั้งหมด → เร็วแม้ข้อมูลหลักแสนแถว
 */
function actionSummary($pdo, $input)
{
    $from = trim($input['from'] ?? '');
    $to   = trim($input['to']   ?? '');
    
    // ===== Validate =====
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) ||
        !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
        respond(false, null, 'กรุณาระบุช่วงวันที่ให้ถูกต้อง');
    }
    
    if (strtotime($from) > strtotime($to)) {
        respond(false, null, 'วันเริ่มต้องไม่เกินวันสิ้นสุด');
    }
    
    // เพิ่มเวลาให้ to = สิ้นวัน (23:59:59)
    $from_dt = $from . ' 00:00:00';
    $to_dt   = $to   . ' 23:59:59';
    
    // ===== 1. จำนวนผู้ป่วยใหม่ (registered_at ในช่วงวันที่) =====
    // ใช้ index idx_phone / idx_name → ไม่มี index บน registered_at
    // แต่ COUNT(*) ไม่ต้อง scan ทั้งตาราง → เร็วอยู่แล้ว
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS total
        FROM tb_patients
        WHERE registered_at BETWEEN :from AND :to
    ");
    $stmt->execute([':from' => $from, ':to' => $to]);
    $newPatients = (int)$stmt->fetchColumn();
    
    // ===== 2. จำนวนนัดทั้งหมด + แยกตามสถานะ (query เดียว GROUP BY) =====
    $stmt = $pdo->prepare("
        SELECT 
            status,
            COUNT(*) AS count
        FROM tb_appointments
        WHERE start_time BETWEEN :from_dt AND :to_dt
        GROUP BY status
    ");
    $stmt->execute([
        ':from_dt' => $from_dt,
        ':to_dt'   => $to_dt,
    ]);
    
    // Initialize ค่าเริ่มต้น (กรณีไม่มีข้อมูลเลย จะได้ไม่ undefined)
    $byStatus = [
        'pending'   => 0,
        'arrived'   => 0,
        'cancelled' => 0,
    ];
    
    $totalAppointments = 0;
    while ($row = $stmt->fetch()) {
        if (isset($byStatus[$row['status']])) {
            $byStatus[$row['status']] = (int)$row['count'];
        }
        $totalAppointments += (int)$row['count'];
    }
    
    // ===== 3. แพทย์ที่มีนัดมากที่สุด (Top 5) =====
    $stmt = $pdo->prepare("
        SELECT 
            d.name AS doctor_name,
            COUNT(a.id) AS appointment_count
        FROM tb_doctors d
        LEFT JOIN tb_appointments a 
            ON a.doctor_id = d.id 
            AND a.start_time BETWEEN :from_dt AND :to_dt
            AND a.status != 'cancelled'
        GROUP BY d.id, d.name
        HAVING appointment_count > 0
        ORDER BY appointment_count DESC
        LIMIT 5
    ");
    $stmt->execute([
        ':from_dt' => $from_dt,
        ':to_dt'   => $to_dt,
    ]);
    $topDoctors = $stmt->fetchAll();
    
    // ===== Response =====
    respond(true, [
        'range' => [
            'from' => $from,
            'to'   => $to,
        ],
        'new_patients'       => $newPatients,
        'total_appointments' => $totalAppointments,
        'by_status'          => $byStatus,
        'top_doctors'        => $topDoctors,
    ]);
}