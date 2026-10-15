<?php 
  $config = require __DIR__ . '/config.php';
  $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";

  //ตั้งค่า PDO OPTIONS
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // โยน exception เมื่อ error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // คื่นค่า error เป็นแบบ array
    PDO::ATTR_EMULATE_PREPARES   => false,    // ใช้ prepared statement จริง
  ];

  // ===== เชื่อมต่อ host db =====
  try{
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
  }catch(PDOException $e) {
    error_log('เชื่อมต่อฐานข้อมูลไม่ได้ ' .$e->getMessage());
    die('ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาติดต่อผู้ดูแลระบบ');
  } 