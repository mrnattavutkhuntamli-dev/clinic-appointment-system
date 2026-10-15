<?php 


function generateHN(PDO $pdo) {
    try{
        $pdo->beginTransaction();

        $stmt = $pdo->query(
            "SELECT hn FROM tb_patients ORDER BY id DESC LIMIT 1 FOR UPDATE"
        );

        $last = $stmt->fetch();

        if($last) {
            // ตัด "HN" (2 ตัวอักษร) ออก เอาแค่ตัวเลข แล้ว +1
            $lastNumber = (int) substr($last['hn'],2);
            $newNumber  = $lastNumber+1;
        }else{
            $newNumber = 1;
        }
        $pdo->commit();
        // format เป็น HN + ตัวเลข 6 หลัก (เติม 0 ข้างหน้าถ้าไม่ครบ)
        return 'HN' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }catch(ErrorException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}