<?php
/**
 * api/doctors.php
 * ดึงรายชื่อแพทย์
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/response.php';

setJsonHeaders();


try {
    $stmt = $pdo->query("SELECT id, name FROM tb_doctors ORDER BY name ASC");
    respond(true, $stmt->fetchAll());
} catch (PDOException $e) {
    error_log('API doctors error: ' . $e->getMessage());
    respond(false, null, 'เกิดข้อผิดพลาดในระบบ');
}