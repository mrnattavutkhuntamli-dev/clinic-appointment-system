/**
 * assets/js/reports.js
 * Frontend AJAX สำหรับหน้ารายงาน
 */

const REPORT_API = 'api/reports.php';

// ============================================
// เริ่มต้น
// ============================================
$(document).ready(function () {
    // Submit form
    $('#reportForm').on('submit', function (e) {
        e.preventDefault();
        loadReport();
    });
    
    // Quick range buttons
    $('.quick-range').on('click', function () {
        const range = $(this).data('range');
        setDateRange(range);
        loadReport();
    });
});

// ============================================
// ตั้งช่วงวันที่แบบเร็ว
// ============================================
function setDateRange(range) {
    const today = new Date();
    let from = new Date();
    
    switch (range) {
        case 'today':
            from = today;
            break;
        case '7days':
            from = new Date(today);
            from.setDate(today.getDate() - 6);
            break;
        case '30days':
            from = new Date(today);
            from.setDate(today.getDate() - 29);
            break;
        case 'thisMonth':
            from = new Date(today.getFullYear(), today.getMonth(), 1);
            break;
    }
    
    $('#dateFrom').val(formatDateInput(from));
    $('#dateTo').val(formatDateInput(today));
}

function formatDateInput(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

// ============================================
// โหลดรายงาน
// ============================================
function loadReport() {
    const from = $('#dateFrom').val();
    const to   = $('#dateTo').val();
    
    if (!from || !to) {
        alertError('ข้อมูลไม่ครบ', 'กรุณาเลือกช่วงวันที่');
        return;
    }
    
    if (from > to) {
        alertError('วันที่ไม่ถูกต้อง', 'วันเริ่มต้องไม่เกินวันสิ้นสุด');
        return;
    }
    
    showSwalLoading('กำลังสร้างรายงาน...');
    
    apiCall(`${REPORT_API}?action=summary&from=${from}&to=${to}`, {}, 'GET')
        .then(res => {
            closeSwalLoading();
            renderReport(res.data);
        })
        .catch(err => {
            closeSwalLoading();
            alertError('โหลดรายงานไม่สำเร็จ', err.message);
        });
}

// ============================================
// Render รายงาน
// ============================================
function renderReport(data) {
    // ซ่อน placeholder, แสดงผลลัพธ์
    $('#reportPlaceholder').addClass('d-none');
    $('#reportResult').removeClass('d-none');
    
    // ช่วงวันที่ (แสดงใน subtitle)
    const rangeText = `${formatDate(data.range.from)} — ${formatDate(data.range.to)}`;
    $('#rangeText1').text(rangeText);
    $('#rangeText2').text(rangeText);
    
    // สถิติหลัก
    $('#statNewPatients').text(data.new_patients.toLocaleString());
    $('#statTotalAppts').text(data.total_appointments.toLocaleString());
    
    // แยกตามสถานะ
    $('#statPending').text(data.by_status.pending.toLocaleString());
    $('#statArrived').text(data.by_status.arrived.toLocaleString());
    $('#statCancelled').text(data.by_status.cancelled.toLocaleString());
    
    // Top doctors
    const $tbody = $('#topDoctorsTable');
    
    if (data.top_doctors.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="3" class="text-center text-muted py-3">
                    ไม่มีข้อมูลนัดในช่วงนี้
                </td>
            </tr>
        `);
        return;
    }
    
    const maxCount = Math.max(...data.top_doctors.map(d => parseInt(d.appointment_count)));
    
    const rows = data.top_doctors.map((d, i) => {
        const count = parseInt(d.appointment_count);
        const percent = maxCount > 0 ? (count / maxCount * 100) : 0;
        const medal = ['🥇', '🥈', '🥉'][i] || `#${i + 1}`;
        
        return `
            <tr>
                <td class="text-center">${medal}</td>
                <td>
                    <div>${escapeHtml(d.doctor_name)}</div>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-primary" 
                             style="width: ${percent}%"></div>
                    </div>
                </td>
                <td class="text-end">
                    <strong>${count.toLocaleString()}</strong>
                </td>
            </tr>
        `;
    }).join('');
    
    $tbody.html(rows);
}