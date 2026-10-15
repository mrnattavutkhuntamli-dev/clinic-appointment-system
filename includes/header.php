<?php
/**
 * includes/header.php
 * ส่วนหัวที่ใช้ร่วมทุกหน้า
 * 
 * วิธีใช้: กำหนด $page_title ก่อน require ไฟล์นี้
 */

if (!isset($page_title)) {
    $page_title = 'Clinic Appointment System';
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — Clinic System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts - Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-hospital text-primary"></i>
            Clinic System
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>" 
                       href="index.php">
                        <i class="bi bi-calendar-check"></i> นัดวันนี้
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'patients.php' ? 'active' : '' ?>" 
                       href="patients.php">
                        <i class="bi bi-people"></i> ผู้ป่วย
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'appointments.php' ? 'active' : '' ?>" 
                       href="appointments.php">
                        <i class="bi bi-calendar-plus"></i> สร้างนัด
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'reports.php' ? 'active' : '' ?>" 
                       href="reports.php">
                        <i class="bi bi-bar-chart"></i> รายงาน
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container pb-5">