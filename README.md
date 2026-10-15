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
| Frontend | HTML5 + Bootstrap 5.3 + jQuery 3.7 |
| Dialog | SweetAlert2 |
| Version Control | Git + GitHub |

---

## 📁 โครงสร้างโปรเจกต์

```
clinic-appointment-system/
├── api/                          # AJAX endpoints (JSON API)
│   ├── patients.php              # CRUD ผู้ป่วย
│   ├── appointments.php          # CRUD นัดหมาย + ตรวจเวลาชน
│   ├── doctors.php               # ดึงรายชื่อแพทย์
│   └── reports.php               # สรุปข้อมูลสำหรับรายงาน
├── assets/
│   ├── css/
│   │   └── style.css             # Custom CSS
│   └── js/
│       ├── common.js             # ฟังก์ชันใช้ร่วม (Toast, SweetAlert2, AJAX wrapper)
│       ├── patients.js           # Frontend หน้าจัดการผู้ป่วย
│       ├── appointments.js       # Frontend หน้าสร้างนัด
│       ├── dashboard.js          # Frontend หน้าบารกานัดวันนี้
│       └── reports.js            # Frontend หน้ารายงาน
├── config/
│   ├── config.php                # ค่าตั้งต้น (DB, timezone)
│   └── db.php                    # เชื่อมต่อฐานข้อมูลด้วย PDO
├── helpers/
│   ├── phone.php                 # Normalize เบอร์โทร
│   ├── hn.php                    # สร้าง HN อัตโนมัติ
│   └── response.php              # Helper ส่ง JSON response
├── includes/
│   ├── header.php                # Navbar + <head>
│   └── footer.php                # ปิด tag + โหลด JS
├── sql/
│   └── clinic_db.sql             # Schema + ข้อมูลตัวอย่าง
├── index.php                     # Dashboard นัดวันนี้
├── patients.php                  # หน้าจัดการผู้ป่วย
├── appointments.php              # หน้าสร้างนัด
├── reports.php                   # หน้ารายงาน
├── .htaccess                     # URL Rewrite (ลบ .php)
├── .gitignore
└── README.md
```

---

## 🚀 วิธีติดตั้งและรัน

### ขั้นตอนที่ 1: เตรียม Environment

**สิ่งที่ต้องมี:**
- PHP 8.0 ขึ้นไป (พร้อม `pdo_mysql` extension)
- MySQL 5.7+ หรือ MariaDB 10.4+
- Web Server (แนะนำ XAMPP / MAMP / Laragon)

**ตรวจสอบเวอร์ชัน:**
```bash
php -v
mysql --version
```

### ขั้นตอนที่ 2: Clone โปรเจกต์

```bash
git clone https://github.com/mrnattavutkhuntamli-dev/clinic-appointment-system.git
cd clinic-appointment-system
```

### ขั้นตอนที่ 3: Import ฐานข้อมูล

**วิธีที่ 1: ผ่าน phpMyAdmin (แนะนำ)**
1. เปิด phpMyAdmin (`http://localhost/phpmyadmin`)
2. ไปที่แท็บ **Import**
3. เลือกไฟล์ `sql/clinic_db.sql`
4. กด **Go**

**วิธีที่ 2: ผ่าน Command Line**
```bash
mysql -u root -p < sql/clinic_db.sql
```

**ผลลัพธ์ที่ควรได้:**
- Database `clinic_db` พร้อม 3 ตาราง
- `tb_doctors` มีข้อมูลแพทย์ตัวอย่าง 4 คน

### ขั้นตอนที่ 4: ตั้งค่า Connection

แก้ไฟล์ `config/config.php` ให้ตรงกับ environment ของคุณ:

```php
<?php
return [
    'db_host'    => 'localhost',
    'db_name'    => 'clinic_db',
    'db_user'    => 'root',
    'db_pass'    => '',          // ใส่รหัสผ่านถ้ามี
    'db_charset' => 'utf8mb4',
    'app_name'   => 'Clinic Appointment System',
    'timezone'   => 'Asia/Bangkok',
];
```

### ขั้นตอนที่ 5: ตั้งค่า URL Rewrite (Optional)

ระบบใช้ `.htaccess` เพื่อลบนามสกุล `.php` ออกจาก URL

**ตรวจสอบให้แน่ใจว่า:**
1. เปิด `mod_rewrite` ใน Apache (`httpd.conf`)
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
2. ตั้ง `AllowOverride All` สำหรับ `htdocs`
   ```apache
   <Directory "C:/xampp/htdocs">
       AllowOverride All
   </Directory>
   ```
3. Restart Apache

> 💡 **ถ้าไม่ต้องการ URL สวย** — ลบ `.htaccess` ทิ้งได้ ระบบยังทำงานปกติ (แต่ URL จะมี `.php`)

### ขั้นตอนที่ 6: รันโปรเจกต์

**ถ้าใช้ XAMPP:**
- วางโปรเจกต์ใน `C:\xampp\htdocs\clinic-appointment-system`
- เปิด `http://localhost/clinic-appointment-system/`

**ถ้าใช้ PHP Built-in Server:**
```bash
php -S localhost:8000
```
- เปิด `http://localhost:8000/`

---

## 📊 โครงสร้างฐานข้อมูล

### ตาราง `tb_patients` (ผู้ป่วย)

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AI) | รหัสภายใน |
| hn | VARCHAR(20) UNIQUE | HN000001 |
| first_name | VARCHAR(80) | ชื่อ |
| last_name | VARCHAR(80) | นามสกุล |
| phone | VARCHAR(10) | เบอร์โทร (normalized) |
| birth_date | DATE | วันเกิด |
| registered_at | DATE | วันที่ลงทะเบียน |

**Indexes:**
- `UNIQUE (hn)` — ป้องกัน HN ซ้ำ
- `INDEX (phone)` — ค้นหาเร็ว
- `INDEX (first_name, last_name)` — ค้นหาชื่อเร็ว

### ตาราง `tb_doctors` (แพทย์)

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AI) | รหัสแพทย์ |
| name | VARCHAR(100) | ชื่อแพทย์ |

### ตาราง `tb_appointments` (นัดหมาย)

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AI) | รหัสนัด |
| patient_id | INT (FK) | อ้างอิง tb_patients |
| doctor_id | INT (FK) | อ้างอิง tb_doctors |
| start_time | DATETIME | เวลาเริ่ม |
| end_time | DATETIME | เวลาสิ้นสุด |
| status | ENUM | pending / arrived / cancelled |
| created_at | TIMESTAMP | เวลาสร้าง |

**Indexes:**
- `INDEX (doctor_id, start_time, end_time)` — ตรวจสอบเวลาชนเร็ว
- `INDEX (start_time)` — ค้นหานัดตามวันเร็ว
- `INDEX (status)` — filter ตามสถานะเร็ว

**Foreign Keys:**
- `fk_appt_patient` → `tb_patients(id)` ON DELETE CASCADE
- `fk_appt_doctor` → `tb_doctors(id)` ON DELETE CASCADE

---

## 🔌 API Endpoints

ทุก endpoint อยู่ที่โฟลเดอร์ `api/` และ return JSON

### Response Format มาตรฐาน

**สำเร็จ:**
```json
{
  "success": true,
  "data": { ... }
}
```

**ผิดพลาด:**
```json
{
  "success": false,
  "error": "ข้อความ error"
}
```

---

### 1. Patients API — `api/patients.php`

| Method | Action | คำอธิบาย |
|--------|--------|----------|
| GET | `?action=list` | ดึงรายชื่อผู้ป่วยทั้งหมด (LIMIT 500) |
| GET | `?action=search&q=สม` | ค้นหาด้วยชื่อ / HN / เบอร์ |
| GET | `?action=get&id=1` | ดึงข้อมูลผู้ป่วย 1 คน |
| POST | `action=create` | เพิ่มผู้ป่วยใหม่ |
| POST | `action=update` | แก้ไขผู้ป่วย |
| POST | `action=delete` | ลบผู้ป่วย |

**ตัวอย่าง — สร้างผู้ป่วยใหม่:**

```bash
POST /api/patients.php
Content-Type: application/json

{
  "action": "create",
  "first_name": "สมชาย",
  "last_name": "ใจดี",
  "phone": "081-234-5678",
  "birth_date": "1990-05-15"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "hn": "HN000001",
    "first_name": "สมชาย",
    "last_name": "ใจดี",
    "phone": "0812345678",
    "birth_date": "1990-05-15"
  }
}
```

---

### 2. Appointments API — `api/appointments.php`

| Method | Action | คำอธิบาย |
|--------|--------|----------|
| GET | `?action=list&date=2026-10-15` | ดึงนัดตามวันที่ |
| GET | `?action=today&doctor_id=2` | ดึงนัดวันนี้ (กรองแพทย์ optional) |
| GET | `?action=get&id=5` | ดึงนัด 1 รายการ |
| POST | `action=create` | สร้างนัด + ตรวจเวลาชน |
| POST | `action=update_status` | เปลี่ยนสถานะ (pending/arrived/cancelled) |

**ตัวอย่าง — สร้างนัด:**

```bash
POST /api/appointments.php
Content-Type: application/json

{
  "action": "create",
  "patient_id": 1,
  "doctor_id": 2,
  "date": "2026-10-15",
  "start_time": "10:00",
  "end_time": "10:30"
}
```

**Response สำเร็จ:**
```json
{
  "success": true,
  "data": {
    "id": 5,
    "patient_hn": "HN000001",
    "doctor_name": "พญ.สมหญิง รักษ์ดี"
  }
}
```

**Response — ตรวจพบเวลาชน:**
```json
{
  "success": false,
  "error": "แพทย์มีนัดในช่วงเวลานี้แล้ว (10:00 - 10:30)"
}
```

---

### 3. Doctors API — `api/doctors.php`

| Method | Action | คำอธิบาย |
|--------|--------|----------|
| GET | (default) | ดึงรายชื่อแพทย์ทั้งหมด |

**Response:**
```json
{
  "success": true,
  "data": [
    { "id": 1, "name": "นพ.สมชาย ใจดี" },
    { "id": 2, "name": "พญ.สมหญิง รักษ์ดี" }
  ]
}
```

---

### 4. Reports API — `api/reports.php`

| Method | Action | คำอธิบาย |
|--------|--------|----------|
| GET | `?action=summary&from=2026-10-01&to=2026-10-31` | สรุปรายงานช่วงวันที่ |

**Response:**
```json
{
  "success": true,
  "data": {
    "range": { "from": "2026-10-01", "to": "2026-10-31" },
    "new_patients": 15,
    "total_appointments": 42,
    "by_status": {
      "pending": 10,
      "arrived": 28,
      "cancelled": 4
    },
    "top_doctors": [
      { "doctor_name": "นพ.สมชาย ใจดี", "appointment_count": "18" }
    ]
  }
}
```

---

### HTTP Status Codes

| Code | ความหมาย |
|------|----------|
| 200 | สำเร็จ (แต่ต้องดู `success: true/false` ใน JSON) |
| 404 | ไม่พบ endpoint |
| 405 | Method ไม่ถูกต้อง |
| 500 | Server error |

---

## 🎯 ฟีเจอร์ที่ทำเสร็จ

### ✅ 1. จัดการข้อมูลผู้ป่วย
- [x] เพิ่มผู้ป่วยใหม่
- [x] แก้ไขข้อมูลผู้ป่วย
- [x] ค้นหาด้วยชื่อ / HN / เบอร์โทร
- [x] สร้าง HN อัตโนมัติ (HN000001)
- [x] Normalize เบอร์โทรทุกรูปแบบเป็น 10 หลัก

### ✅ 2. นัดหมาย
- [x] สร้างนัด (เลือกผู้ป่วย + แพทย์ + วันเวลา)
- [x] ตรวจสอบเวลาชนของแพทย์คนเดียวกัน
- [x] ยกเลิกนัด (นับเป็น cancelled ไม่ชน)

### ✅ 3. Dashboard นัดวันนี้
- [x] แสดงรายการนัดวันนี้ เรียงตามเวลา
- [x] กรองตามแพทย์
- [x] เปลี่ยนสถานะ (รอ / มาแล้ว / ยกเลิก)

### ✅ 4. รายงานสรุป
- [x] เลือกช่วงวันที่
- [x] แสดงจำนวนผู้ป่วยใหม่
- [x] แสดงจำนวนนัดทั้งหมด + แยกตามสถานะ
- [x] Query เร็วด้วย Index (รองรับข้อมูลหลักแสนแถว)

---

## 🔍 หมายเหตุการออกแบบ

### เบอร์โทร (Phone Normalization)

รองรับทุกรูปแบบ → เก็บเป็น 10 หลักเสมอ:

| Input | Output |
|-------|--------|
| `081-234-5678` | `0812345678` |
| `+66812345678` | `0812345678` |
| `66 81 234 5678` | `0812345678` |
| `0812345678` | `0812345678` |

### HN Generation

- รูปแบบ: `HN000001` (HN + ตัวเลข 6 หลัก)
- ใช้ `SELECT ... FOR UPDATE` ใน transaction ป้องกัน HN ซ้ำ

### การตรวจเวลาชนของแพทย์

ใช้ query ตรวจสอบ **overlap** 2 ช่วงเวลา:

```
ช่วงเวลา A ชนกับ B เมื่อ:
A.start < B.end AND A.end > B.start
```

ใช้ index `(doctor_id, start_time, end_time)` เพื่อให้ query เร็ว

### การรองรับข้อมูลหลักแสนแถว

- ทุกคอลัมน์ที่ค้นหา/กรอง มี index
- รายงานใช้ aggregate function (`COUNT`, `GROUP BY`) โดยตรง ไม่ดึง raw data
- ใช้ `prepared statement` ป้องกัน SQL injection + query plan cache

---

## 🤖 ส่วนที่ใช้ AI ช่วย

ตามที่โจทย์กำหนด ขอระบุส่วนที่ใช้ AI (ChatGPT/Claude) ช่วย:

| ส่วน | AI ช่วยอะไร | ตรวจสอบเอง |
|------|-------------|------------|
| Database Schema | แนะนำโครงสร้าง index + foreign key | ✅ ตรวจแล้วว่า index ตรงกับ query ที่ใช้จริง |
| Helper Functions | ร่างฟังก์ชัน `normalizePhone()` และ `generateHN()` | ✅ ทดสอบ edge case (11 หลัก, ไม่เริ่มด้วย 0, HN ซ้ำ) |
| ตรวจเวลาชน | แนะนำ pattern overlap `A.start < B.end AND A.end > B.start` | ✅ ทดสอบเคสซ้อนบางส่วน (10:00–11:00 vs 10:30–11:30) |
| README | ร่างโครงสร้างและ content | ✅ ตรวจให้ตรงกับโค้ดจริง |
| CSS Styling | ร่าง style พื้นฐาน | ✅ ปรับสี/ขนาดตามต้องการ |

**หมายเหตุ:** AI ใช้เป็น "ผู้ช่วยร่าง" เท่านั้น — โค้ดทุกส่วนผ่านการอ่านและปรับด้วยตัวเอง

---

## 📜 ภาพรวม Commit History

โปรเจกต์นี้ commit ทีละส่วนตามลำดับการพัฒนา:

| # | Commit | รายละเอียด |
|---|--------|-----------|
| 1 | `feat: add database schema` | สร้าง 3 ตาราง + index + FK + ข้อมูลแพทย์ตัวอย่าง |
| 2 | `chore: add configuration file` | `config/config.php` |
| 3 | `feat: add database connection` | `config/db.php` ด้วย PDO |
| 4 | `feat: add phone and HN helpers` | `helpers/phone.php`, `helpers/hn.php` |
| 5 | `feat: add common JS helpers` | `assets/js/common.js` + Toast + SweetAlert2 |
| 6 | `style: add custom CSS` | `assets/css/style.css` |
| 7 | `chore: add header and footer layout` | `includes/header.php`, `includes/footer.php` |
| 8 | `feat: add patient management` | `api/patients.php`, `patients.php`, `assets/js/patients.js` |
| 9 | `feat: add appointment management` | `api/appointments.php`, `appointments.php`, `assets/js/appointments.js` |
| 10 | `feat: add today dashboard` | `index.php`, `assets/js/dashboard.js` |
| 11 | `feat: add summary reports` | `api/reports.php`, `reports.php`, `assets/js/reports.js` |
| 12 | `refactor: extract response helper` | `helpers/response.php` |
| 13 | `chore: add URL rewrite` | `.htaccess` + แก้ `header.php` |
| 14 | `docs: improve README` | เอกสาร |

**หลักการ commit:**
- แยก commit ทีละส่วน (feature) ไม่ยัดทั้งหมดใน commit เดียว
- ใช้ Conventional Commits: `feat:`, `fix:`, `docs:`, `style:`, `chore:`, `refactor:`
- ข้อความสั้น กระชับ อ่านเข้าใจง่าย

---

## ⚠️ ข้อจำกัดที่รู้อยู่

ส่วนที่ยังไม่ได้ทำหรือจำกัดไว้ (เพื่อความโปร่งใส):

- ❌ **ไม่มีระบบ Authentication** — ใครเข้า URL ได้ก็ใช้ได้ (โจทย์ไม่ได้ต้องการ)
- ❌ **ไม่มีหน้าจัดการแพทย์** — ใส่แพทย์ตัวอย่างใน DB ตรงๆ (ตามโจทย์บอก)
- ❌ **ไม่มี Pagination** — ถ้าผู้ป่วยเยอะ อาจโหลดช้า (แต่ query มี LIMIT 500)
- ❌ **ไม่มี Unit Tests** — ใช้ manual test ผ่าน UI แทน
- ❌ **ไม่มี Responsive Design** — CSS รองรับ desktop เป็นหลัก
- ⚠️ **`config/config.php` เก็บรหัสผ่านแบบ plain text** — สำหรับโปรเจกต์จริงควรใช้ `.env`

---

## 🧪 วิธีทดสอบฟีเจอร์

### ทดสอบที่ 1: เพิ่มผู้ป่วย
1. ไปที่หน้า "ผู้ป่วย" → กด "เพิ่มผู้ป่วย"
2. กรอกข้อมูล + เบอร์โทรรูปแบบต่างๆ (`081-234-5678`, `+66812345678`)
3. บันทึก → ตรวจสอบว่า HN ถูกสร้างอัตโนมัติ และเบอร์ถูก normalize

### ทดสอบที่ 2: สร้างนัด
1. ไปที่หน้า "สร้างนัด"
2. เลือกผู้ป่วย + แพทย์ + เวลา
3. ลองสร้างนัดซ้อนเวลากับนัดเดิมของแพทย์คนเดิม → ต้อง error

### ทดสอบที่ 3: Dashboard
1. ไปที่หน้าแรก
2. ต้องเห็นนัดของวันนี้ เรียงตามเวลา
3. ลองเปลี่ยนสถานะ → ต้องบันทึกทันที

### ทดสอบที่ 4: รายงาน
1. ไปที่หน้า "รายงาน"
2. เลือกช่วงวันที่ → กด "ดูรายงาน"
3. ต้องเห็นจำนวนผู้ป่วยใหม่ + นัดแยกตามสถานะ

---

## 🔧 Troubleshooting

### ❌ ปัญหา: เชื่อมต่อฐานข้อมูลไม่ได้

**อาการ:** หน้าเว็บขึ้นข้อความ "ไม่สามารถเชื่อมต่อฐานข้อมูลได้"

**แก้ไข:**
1. ตรวจสอบว่า MySQL เปิดอยู่ (XAMPP Control Panel)
2. ตรวจสอบ `config/config.php` ว่า `db_host`, `db_user`, `db_pass` ถูกต้อง
3. ทดสอบ import SQL อีกครั้ง

### ❌ ปัญหา: URL Rewrite ไม่ทำงาน (404)

**อาการ:** เปิด `/patients` แล้วเจอ 404

**แก้ไข:**
1. เปิด `mod_rewrite` ใน `httpd.conf`
2. ตั้ง `AllowOverride All`
3. Restart Apache
4. ตรวจสอบว่ามีไฟล์ `.htaccess` ที่ root

### ❌ ปัญหา: Normalize เบอร์โทรไม่ทำงาน

**อาการ:** เบอร์ `081-234-5678` บันทึกเป็น `081-234-5678` (ไม่ตัดขีด)

**แก้ไข:**
1. ตรวจสอบ `helpers/phone.php` — บรรทัด `preg_replace('/\D/', '', $phone)`
2. ตรวจสอบว่า `strlen($digits) !== 10 || $digits[0] !== '0'` — **ต้องมี quote รอบ `'0'`**

### ❌ ปัญหา: ตรวจเวลาชนไม่ทำงาน

**อาการ:** สร้างนัดซ้อนเวลาได้ ทั้งที่ควรจะ error

**แก้ไข:**
1. ตรวจสอบ query ใน `actionCreate()` ใน `api/appointments.php`
2. ต้องมีเงื่อนไข: `start_time < :new_end AND end_time > :new_start`
3. ต้องมี `AND status != 'cancelled'`

### ❌ ปัญหา: AJAX ไม่ทำงาน

**อาการ:** กดปุ่มแล้วไม่มีอะไรเกิดขึ้น

**แก้ไข:**
1. เปิด **DevTools (F12) → Console** — ดูว่ามี error อะไร
2. เปิด **Network tab** — ดู request `api/*.php` ว่ามี status 200
3. ตรวจสอบว่า jQuery + Bootstrap + SweetAlert2 โหลดครบ (ใน footer.php)

---

## 📄 License

โปรเจกต์นี้เป็นงานทดสอบ ไม่มี license สำหรับใช้งานจริง

---

## 👤 ผู้พัฒนา

- **ชื่อ:** นายณัฐวุฒิ ขุนตำลิ (Mr. Nattavut Khuntamli)
- **อีเมล:** mr.nattavut.khuntamli@gmail.com
- **GitHub:** [mrnattavutkhuntamli-dev](https://github.com/mrnattavutkhuntamli-dev)
- **วันที่ส่ง:** 16 ตุลาคม 2569

---

*ขอบคุณที่ให้โอกาสในการทดสอบครับ 🙏*