/**
 * assets/js/dashboard.js
 * Frontend AJAX สำหรับ Dashboard นัดวันนี้
 */

const APPT_API = 'api/appointments.php';
const DOCTOR_API = 'api/doctors.php';

// ============================================
// เริ่มต้น
// ============================================
$(document).ready(function () {
    loadDoctorsDropdown();
    loadTodayAppointments();
    
    // เปลี่ยนแพทย์ → โหลดใหม่
    $('#filterDoctor').on('change', loadTodayAppointments);
    
    // รีเฟรช
    $('#btnRefresh').on('click', loadTodayAppointments);
    
    // Auto refresh ทุก 60 วินาที
    setInterval(loadTodayAppointments, 60000);
});

// ============================================
// โหลด Dropdown แพทย์
// ============================================
function loadDoctorsDropdown() {
    apiCall(DOCTOR_API + '?action=list', {}, 'GET')
        .then(res => {
            const $select = $('#filterDoctor');
            const currentValue = $select.val();
            
            $select.html('<option value="">-- ทั้งหมด --</option>');
            res.data.forEach(d => {
                $select.append(`<option value="${d.id}">${escapeHtml(d.name)}</option>`);
            });
            
            // คงค่าเดิม
            if (currentValue) $select.val(currentValue);
        })
        .catch(err => console.error('Load doctors error:', err));
}

// ============================================
// โหลดนัดวันนี้
// ============================================
function loadTodayAppointments() {
    const doctorId = $('#filterDoctor').val();
    let url = APPT_API + '?action=today';
    if (doctorId) url += '&doctor_id=' + encodeURIComponent(doctorId);
    
    showTableLoading();
    
    apiCall(url, {}, 'GET')
        .then(res => {
            renderAppointments(res.data.appointments);
            updateSummary(res.data.summary);
        })
        .catch(err => {
            showTableError(err.message);
        });
}

// ============================================
// Render ตาราง
// ============================================
function renderAppointments(appointments) {
    const $tbody = $('#appointmentsTable');
    
    if (appointments.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <p>ไม่มีนัดในวันนี้</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    const rows = appointments.map(a => {
        const start = formatTime(a.start_time);
        const end   = formatTime(a.end_time);
        const isCancelled = a.status === 'cancelled';
        
        return `
            <tr class="${isCancelled ? 'opacity-50' : ''}">
                <td>
                    <strong>${start}</strong> - ${end}
                </td>
                <td><span class="badge bg-secondary">${escapeHtml(a.hn)}</span></td>
                <td>${escapeHtml(a.first_name)} ${escapeHtml(a.last_name)}</td>
                <td>${escapeHtml(formatPhoneDisplay(a.phone))}</td>
                <td>${escapeHtml(a.doctor_name)}</td>
                <td>
                    <select class="form-select form-select-sm status-select" 
                            data-id="${a.id}"
                            style="width: auto; min-width: 100px;">
                        <option value="pending"   ${a.status === 'pending'   ? 'selected' : ''}>รอ</option>
                        <option value="arrived"   ${a.status === 'arrived'   ? 'selected' : ''}>มาแล้ว</option>
                        <option value="cancelled" ${a.status === 'cancelled' ? 'selected' : ''}>ยกเลิก</option>
                    </select>
                </td>
            </tr>
        `;
    }).join('');
    
    $tbody.html(rows);
    
    // ผูก event กับ status dropdown
    $('.status-select').off('change').on('change', handleStatusChange);
}

function showTableLoading() {
    $('#appointmentsTable').html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                กำลังโหลด...
            </td>
        </tr>
    `);
}

function showTableError(message) {
    $('#appointmentsTable').html(`
        <tr>
            <td colspan="6" class="text-center py-4 text-danger">
                <i class="bi bi-exclamation-triangle"></i>
                ${escapeHtml(message)}
            </td>
        </tr>
    `);
}

// ============================================
// อัปเดตสถิติ
// ============================================
function updateSummary(summary) {
    $('#statTotal').text(summary.total);
    $('#statPending').text(summary.pending);
    $('#statArrived').text(summary.arrived);
    $('#statCancelled').text(summary.cancelled);
}

// ============================================
// เปลี่ยนสถานะ
// ============================================
function handleStatusChange() {
    const $select = $(this);
    const id     = parseInt($select.data('id'));
    const status = $select.val();
    const previous = $select.data('previous') || $select.find('option[selected]').val() || 'pending';
    
    // Disable ระหว่างอัปเดต
    $select.prop('disabled', true);
    
    apiCall(APPT_API, { action: 'update_status', id: id, status: status })
        .then(() => {
            showToast('อัปเดตสถานะสำเร็จ', 'success');
            $select.data('previous', status);
            
            // อัปเดตสถิติโดยโหลดใหม่ (เร็วและแม่นยำ)
            loadTodayAppointments();
        })
        .catch(err => {
            alertError('อัปเดตไม่สำเร็จ', err.message);
            $select.val(previous);
        })
        .finally(() => {
            $select.prop('disabled', false);
        });
}