<?php 
$page_title = 'สร้างนัดหมาย';
$extra_js = ['assets/js/appointments.js'];
require_once 'includes/header.php';
?>

<div class="row g-4">
    <!-- ============================================
         ฟอร์มสร้างนัด
         ============================================ -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-calendar-plus text-primary"></i> สร้างนัดใหม่
            </div>
            <div class="card-body">
                <form id="appointmentForm">
                    <div class="mb-3">
                        <label class="form-label required">ผู้ป่วย</label>
                        <select class="form-select" id="patientId" name="patient_id" required>
                            <option value="">-- กำลังโหลด --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">แพทย์</label>
                        <select class="form-select" id="doctorId" name="doctor_id" required>
                            <option value="">-- กำลังโหลด --</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required">วันที่</label>
                        <input type="date" 
                               class="form-control" 
                               id="apptDate"
                               name="date"
                               value="<?= date('Y-m-d') ?>"
                               required>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label required">เวลาเริ่ม</label>
                            <input type="time" 
                                   class="form-control" 
                                   id="startTime"
                                   name="start_time"
                                   value="09:00"
                                   required>
                        </div>
                        <div class="col-6">
                            <label class="form-label required">เวลาสิ้นสุด</label>
                            <input type="time" 
                                   class="form-control" 
                                   id="endTime"
                                   name="end_time"
                                   value="09:30"
                                   required>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="bi bi-check-lg"></i> บันทึกนัด
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ============================================
         รายการนัด
         ============================================ -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span><i class="bi bi-list-check text-primary"></i> รายการนัด</span>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">วันที่:</label>
                    <input type="date" 
                           class="form-control form-control-sm" 
                           id="filterDate"
                           style="width: auto;"
                           value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>เวลา</th>
                                <th>ผู้ป่วย</th>
                                <th>แพทย์</th>
                                <th>สถานะ</th>
                                <th class="text-end">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="appointmentsTable">
                            <tr>
                                <td colspan="5" class="text-center py-4">
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
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>