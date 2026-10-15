<?php 
$page_title = 'จัดการผู้ป่วย';
$extra_js = ['assets/js/patients.js'];
require_once 'includes/header.php';
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-people-fill text-primary"></i> รายชื่อผู้ป่วย
        </span>
        <button class="btn btn-primary btn-sm" id="btnAddPatient">
            <i class="bi bi-plus-lg"></i> เพิ่มผู้ป่วย
        </button>
    </div>
    
    <div class="card-body">
        <!-- ช่องค้นหา -->
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" 
                       class="form-control" 
                       id="searchInput"
                       placeholder="ค้นหาด้วยชื่อ, HN หรือเบอร์โทร...">
            </div>
            <small class="text-muted">พิมพ์เพื่อค้นหา (จะค้นหาอัตโนมัติ)</small>
        </div>
        
        <!-- ตารางผู้ป่วย -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>HN</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>เบอร์โทร</th>
                        <th>วันเกิด</th>
                        <th>วันที่ลงทะเบียน</th>
                        <th class="text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody id="patientsTable">
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            กำลังโหลด...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="text-muted small" id="resultCount"></div>
    </div>
</div>

<!-- =============================================
     Modal: เพิ่ม/แก้ไขผู้ป่วย
     ============================================= -->
<div class="modal fade" id="patientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="patientForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">เพิ่มผู้ป่วยใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <input type="hidden" id="patientId" name="id">
                    
                    <div class="alert alert-info d-none" id="hnDisplay">
                        <strong>HN:</strong> <span id="hnValue"></span>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">ชื่อ</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="firstName"
                                   name="first_name" 
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required">นามสกุล</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="lastName"
                                   name="last_name" 
                                   required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label required">เบอร์โทรศัพท์</label>
                            <input type="tel" 
                                   class="form-control" 
                                   id="phone"
                                   name="phone"
                                   placeholder="081-234-5678 หรือ +66812345678"
                                   required>
                            <small class="text-muted">
                                รองรับทุกรูปแบบ — ระบบจะจัดเก็บเป็น 10 หลักอัตโนมัติ
                            </small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label required">วันเกิด</label>
                            <input type="date" 
                                   class="form-control" 
                                   id="birthDate"
                                   name="birth_date"
                                   max="<?= date('Y-m-d') ?>"
                                   required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSave">
                        <i class="bi bi-check-lg"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>