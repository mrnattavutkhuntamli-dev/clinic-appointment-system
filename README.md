# 🏥 Clinic Appointment System

ระบบนัดหมายคลินิกขนาดเล็ก พัฒนาด้วย **PHP + MySQL** (Vanilla PHP ไม่ใช้ Framework)

---

## 📋 ภาพรวม

ระบบนี้เป็นโจทย์ทดสอบสำหรับตำแหน่ง Programmer รองรับฟีเจอร์หลัก 4 ข้อ:

1. **จัดการข้อมูลผู้ป่วย** — เพิ่ม / แก้ไข / ค้นหา (ชื่อ, HN, เบอร์โทร)
2. **นัดหมาย** — สร้างนัด, ป้องกันเวลาชน, ยกเลิกได้
3. **หน้ารายการนัดของวันนี้** — แสดงนัดวันนี้ กรองตามแพทย์ เปลี่ยนสถานะได้
4. **รายงานสรุป** — เลือกช่วงวันที่ แสดงจำนวนผู้ป่วยใหม่ + นัดแยกตามสถานะ

---

## 🛠️ เทคโนโลยีที่ใช้

| ส่วน | เทคโนโลยี |
|------|-----------|
| Backend | PHP 8.0 (Vanilla, Procedural) |
| Database | MySQL / MariaDB (PDO) |
| Frontend | HTML5 + CSS3 Bootstrap 5.3 |
| Version Control | Git + GitHub |

---

## 📁 โครงสร้างโปรเจกต์
````  
clinic-appointment-system/
├── assets
│   ├── css        
│   ├   ├── style.css         ← ไฟล์จัดการ css
│   ├── js        
├   ├── common.js         ← 
├── config/
│   ├── config.php         ← ไฟล์ตั้งค่า (ค่าคงที่)
│   └── db.php             ← ไฟล์เชื่อมต่อ DB
├── helpers/
│   ├── phone.php    ← จัดการเบอร์โทรศัพท์
│   └── hn.php  ←  สร้างรหัสผู้ป่วย
├── includes/
│   ├── header.php    ← ส่วนหัวของหน้าเว็บ
│   └── footer.php  ←  ส่วนท้ายของหน้าเว็บ
├── sql/
│   └── clinic_db.sql
├── api/
│   ├── patients.php    ← จัดการเบอร์โทรศัพท์
│   ├── appointments.php    ← จัดการเบอร์โทรศัพท์
│   ├── update_status.php    ← จัดการเบอร์โทรศัพท์
│   ├── reports.php    ← จัดการเบอร์โทรศัพท์
├── .gitignore
└── README.md
```` 

## 📁 ตั้งค่าไฟล์ config.php
##### ก่อนจะทำการเชื่อมต่อระบบต้องมีฐานข้อมูลก่อนโดย สามารถ import ฐานข้อมูลได้ที่ตำแหน่ง sql/clinic_db.sql
##### จากนั้นไปหาโฟลเดอร์ config และไฟล์ที่ชื่อ config.test เพื่อตั้งค่าระบบสำหรับเชื่อมค่อฐานข้อมูล และเปลี่ยนนามสกุลไฟล์เป็น .php
```
<?php 
 return [
    'db_host' => ',  // ชื่อ host
    'db_name' => '', // ชื่อฐานข้อมูล
    'db_user' => '', // username
    'db_pass' => '', // password
 ];
```

