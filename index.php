<?php 
$page_title = 'นัดวันนี้';
$extra_js = ['assets/js/dashboard.js'];
require_once 'includes/header.php';
?>

<div class="row g-4">
    <!-- ============================================
         หัวข้อ + ตัวกรอง
         ============================================ -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>
                    <i class="bi bi-calendar-check text-primary"></i>
                    นัดวันนี้ — <?= date('d/m/Y') ?>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">แพทย์:</label>
                    <select class="form-select form-select-sm" id="filterDoctor" style="width: auto;">
                        <option value="">-- ทั้งหมด --</option>
                    </select>
                    <button class="btn btn-sm btn-outline-secondary" id="btnRefresh" title="รีเฟรช">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ============================================
         สรุปสถิติ
         ============================================ -->
    <div class="col-12">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="card stat-card stat-total">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">นัดทั้งหมด</div>
                                <div class="stat-value" id="statTotal">0</div>
                            </div>
                            <i class="bi bi-calendar-check stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="card stat-card stat-pending">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">รอ</div>
                                <div class="stat-value" id="statPending">0</div>
                            </div>
                            <i class="bi bi-hourglass-split stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="card stat-card stat-arrived">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">มาแล้ว</div>
                                <div class="stat-value" id="statArrived">0</div>
                            </div>
                            <i class="bi bi-check-circle stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6">
                <div class="card stat-card stat-cancelled">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">ยกเลิก</div>
                                <div class="stat-value" id="statCancelled">0</div>
                            </div>
                            <i class="bi bi-x-circle stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ============================================
         ตารางนัด
         ============================================ -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>เวลา</th>
                                <th>HN</th>
                                <th>ผู้ป่วย</th>
                                <th>เบอร์โทร</th>
                                <th>แพทย์</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="appointmentsTable">
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary"></div>
                                    กำลังโหลด...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>