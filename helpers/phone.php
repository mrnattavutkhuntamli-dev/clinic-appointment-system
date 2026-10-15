<?php 


function normalizePhone(string $phone) {
    // ลบอักขระที่ไม่ใช่ตัวเลขทั้งหมด (ช่องว่าง, -, +, ., วงเล็บ ฯลฯ)
    $digits = preg_replace('/\D/', '', $phone);
    if (strlen($digits) === 11 && strpos($digits, '66') === 0) {
        $digits = '0' . substr($digits, 2);
    }

   if (strlen($digits) !== 10 || $digits[0] !== '0') {
        return false;
    }

    return $digits;
}

function formatPhoneDisplay(string $phone) {
    if (strlen($phone) !== 10) {
        return $phone;
    }
     return substr($phone, 0, 3) . '-' 
         . substr($phone, 3, 3) . '-' 
         . substr($phone, 6, 4);
}