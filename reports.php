<?php 
$page_title = 'รายงานสรุป';
$extra_js = ['assets/js/reports.js'];
require_once 'includes/header.php';
?>

<div class="row g-4">
    <!-- ============================================
         ตัวกรองช่วงวันที่
         ============================================ -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-funnel text-primary"></i> เลือกช่วงวันที่
            </div>
            <div class="card-body">
                <form id="reportForm" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">จากวันที่</label>
                        <input type="date" 
                               class="form-control" 
                               id="dateFrom"
                               value="<?= date('Y-m-01') ?>"
                               required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ถึงวันที่</label>
                        <input type="date" 
                               class="form-control" 
                               id="dateTo"
                               value="<?= date('Y-m-d') ?>"
                               required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> ดูรายงาน
                        </button>
                    </div>
                </form>
                
                <!-- Quick ranges -->
                <div class="mt-3 d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-outline-secondary quick-range" data-range="today">
                        วันนี้
                    </button>
                    <button class="btn btn-sm btn-outline-secondary quick-range" data-range="7days">
                        7 วันล่าสุด
                    </button>
                    <button class="btn btn-sm btn-outline-secondary quick-range" data-range="30days">
                        30 วันล่าสุด
                    </button>
                    <button class="btn btn-sm btn-outline-secondary quick-range" data-range="thisMonth">
                        เดือนนี้
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ============================================
         ส่วนแสดงผลรายงาน (จะแสดงเมื่อมีข้อมูล)
         ============================================ -->
    <div class="col-12 d-none" id="reportResult">
        
        <!-- สถิติหลัก -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card stat-card stat-total">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">ผู้ป่วยใหม่</div>
                                <div class="stat-value" id="statNewPatients">0</div>
                                <small class="text-muted" id="rangeText1"></small>
                            </div>
                            <i class="bi bi-person-plus stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card stat-card stat-arrived">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-label">นัดทั้งหมด</div>
                                <div class="stat-value" id="statTotalAppts">0</div>
                                <small class="text-muted" id="rangeText2"></small>
                            </div>
                            <i class="bi bi-calendar-check stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- แยกตามสถานะ -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-pie-chart text-primary"></i> นัดแยกตามสถานะ
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="status-box status-pending">
                            <i class="bi bi-hourglass-split"></i>
                            <div>
                                <div class="status-label">รอ</div>
                                <div class="status-count" id="statPending">0</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="status-box status-arrived">
                            <i class="bi bi-check-circle"></i>
                            <div>
                                <div class="status-label">มาแล้ว</div>
                                <div class="status-count" id="statArrived">0</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="status-box status-cancelled">
                            <i class="bi bi-x-circle"></i>
                            <div>
                                <div class="status-label">ยกเลิก</div>
                                <div class="status-count" id="statCancelled">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Doctors -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-trophy text-warning"></i> แพทย์ที่มีนัดมากที่สุด (Top 5)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>แพทย์</th>
                                <th class="text-end">จำนวนนัด</th>
                            </tr>
                        </thead>
                        <tbody id="topDoctorsTable"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ============================================
         Placeholder (ก่อนกดดูรายงาน)
         ============================================ -->
    <div class="col-12" id="reportPlaceholder">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-bar-chart" style="font-size: 4rem; color: #dee2e6;"></i>
                <p class="text-muted mt-3 mb-0">
                    เลือกช่วงวันที่แล้วกด <strong>"ดูรายงาน"</strong> เพื่อแสดงผล
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>