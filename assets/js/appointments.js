/**
 * assets/js/appointments.js
 * Frontend AJAX สำหรับหน้าสร้างนัด
 */

const APPT_API = 'api/appointments.php';
const PATIENT_API = 'api/patients.php';

// ============================================
// เริ่มต้น
// ============================================
$(document).ready(function () {
    loadPatientsDropdown();
    loadDoctorsDropdown();
    loadAppointments();
    
    // Submit form
    $('#appointmentForm').on('submit', handleCreateAppointment);
    
    // เปลี่ยนวันที่ → โหลดนัดใหม่
    $('#filterDate').on('change', loadAppointments);
    
    // ตั้งเวลาสิ้นสุดอัตโนมัติ (start + 30 นาที)
    $('#startTime').on('change', function () {
        const start = $(this).val();
        if (!start) return;
        
        const [h, m] = start.split(':').map(Number);
        const endMinutes = h * 60 + m + 30;
        const endH = String(Math.floor(endMinutes / 60)).padStart(2, '0');
        const endM = String(endMinutes % 60).padStart(2, '0');
        
        $('#endTime').val(`${endH}:${endM}`);
    });
});

// ============================================
// โหลด Dropdown
// ============================================

function loadPatientsDropdown() {
    apiCall(PATIENT_API + '?action=list', {}, 'GET')
        .then(res => {
            const $select = $('#patientId');
            $select.html('<option value="">-- เลือกผู้ป่วย --</option>');
            
            res.data.forEach(p => {
                $select.append(`
                    <option value="${p.id}">
                        ${escapeHtml(p.hn)} — ${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)}
                    </option>
                `);
            });
        })
        .catch(err => {
            console.error('Load patients error:', err);
            $('#patientId').html('<option value="">-- โหลดไม่สำเร็จ --</option>');
        });
}

function loadDoctorsDropdown() {
    // ใช้ endpoint ใหม่ที่เราจะสร้าง หรือ query ตรง
    apiCall('api/doctors.php?action=list', {}, 'GET')
        .then(res => {
            const $select = $('#doctorId');
            $select.html('<option value="">-- เลือกแพทย์ --</option>');
            
            res.data.forEach(d => {
                $select.append(`<option value="${d.id}">${escapeHtml(d.name)}</option>`);
            });
        })
        .catch(err => {
            console.error('Load doctors error:', err);
            $('#doctorId').html('<option value="">-- โหลดไม่สำเร็จ --</option>');
        });
}

// ============================================
// โหลดรายการนัด
// ============================================

function loadAppointments() {
    const date = $('#filterDate').val();
    if (!date) return;
    
    showTableLoading();
    
    apiCall(APPT_API + '?action=list&date=' + encodeURIComponent(date), {}, 'GET')
        .then(res => {
            renderAppointments(res.data);
            $('#resultCount').text(`พบ ${res.data.length} นัด`);
        })
        .catch(err => {
            showTableError(err.message);
        });
}

// ============================================
// Render
// ============================================

function renderAppointments(appointments) {
    const $tbody = $('#appointmentsTable');
    
    if (appointments.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <p>ไม่มีนัดในวันนี้</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    const statusMap = {
        pending:   { text: 'รอ',     cls: 'badge-pending' },
        arrived:   { text: 'มาแล้ว',  cls: 'badge-arrived' },
        cancelled: { text: 'ยกเลิก',  cls: 'badge-cancelled' }
    };
    
    const rows = appointments.map(a => {
        const s = statusMap[a.status] || statusMap.pending;
        const start = formatTime(a.start_time);
        const end   = formatTime(a.end_time);
        
        return `
            <tr>
                <td>
                    <strong>${start}</strong> - ${end}
                </td>
                <td>
                    <span class="badge bg-secondary">${escapeHtml(a.hn)}</span><br>
                    <small>${escapeHtml(a.first_name)} ${escapeHtml(a.last_name)}</small>
                </td>
                <td>${escapeHtml(a.doctor_name)}</td>
                <td>
                    <select class="form-select form-select-sm status-select" 
                            data-id="${a.id}"
                            style="width: auto;">
                        <option value="pending"   ${a.status === 'pending'   ? 'selected' : ''}>รอ</option>
                        <option value="arrived"   ${a.status === 'arrived'   ? 'selected' : ''}>มาแล้ว</option>
                        <option value="cancelled" ${a.status === 'cancelled' ? 'selected' : ''}>ยกเลิก</option>
                    </select>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-secondary btn-icon"
                            onclick="copyHN('${escapeHtml(a.hn)}')"
                            title="คัดลอก HN">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
    
    $tbody.html(rows);
    
    // ผูก event กับ status dropdown (ใช้ event delegation)
    $('.status-select').off('change').on('change', handleStatusChange);
}

function showTableLoading() {
    $('#appointmentsTable').html(`
        <tr>
            <td colspan="5" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                กำลังโหลด...
            </td>
        </tr>
    `);
}

function showTableError(message) {
    $('#appointmentsTable').html(`
        <tr>
            <td colspan="5" class="text-center py-4 text-danger">
                <i class="bi bi-exclamation-triangle"></i>
                ${escapeHtml(message)}
            </td>
        </tr>
    `);
}

// ============================================
// สร้างนัด
// ============================================

function handleCreateAppointment(e) {
    e.preventDefault();
    
    const data = {
        action:     'create',
        patient_id: parseInt($('#patientId').val()),
        doctor_id:  parseInt($('#doctorId').val()),
        date:       $('#apptDate').val(),
        start_time: $('#startTime').val(),
        end_time:   $('#endTime').val()
    };
    
    // Validate เบื้องต้น
    if (!data.patient_id || !data.doctor_id || !data.date || !data.start_time || !data.end_time) {
        alertError('ข้อมูลไม่ครบ', 'กรุณากรอกข้อมูลให้ครบทุกช่อง');
        return;
    }
    
    // Disable ปุ่ม
    const $btn = $('#btnSubmit');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...');
    
    apiCall(APPT_API, data)
        .then(res => {
            showToast(`สร้างนัดสำเร็จ (${res.data.patient_hn})`, 'success');
            $('#appointmentForm')[0].reset();
            $('#apptDate').val(data.date);
            $('#startTime').val('09:00');
            $('#endTime').val('09:30');
            
            // รีเฟรชตาราง (ถ้าวันที่ตรงกัน)
            if ($('#filterDate').val() === data.date) {
                loadAppointments();
            } else {
                $('#filterDate').val(data.date);
                loadAppointments();
            }
        })
        .catch(err => {
            alertError('สร้างนัดไม่สำเร็จ', err.message);
        })
        .finally(() => {
            $btn.prop('disabled', false).html(originalHtml);
        });
}

// ============================================
// เปลี่ยนสถานะ
// ============================================

function handleStatusChange() {
    const $select = $(this);
    const id     = parseInt($select.data('id'));
    const status = $select.val();
    
    const originalValue = $select.data('original') || 'pending';
    
    apiCall(APPT_API, { action: 'update_status', id: id, status: status })
        .then(() => {
            showToast('อัปเดตสถานะสำเร็จ', 'success');
            $select.data('original', status);
        })
        .catch(err => {
            alertError('อัปเดตไม่สำเร็จ', err.message);
            $select.val(originalValue); // revert
        });
}

// ============================================
// Utilities
// ============================================

function copyHN(hn) {
    navigator.clipboard.writeText(hn).then(() => {
        showToast(`คัดลอก ${hn} แล้ว`, 'success');
    });
}