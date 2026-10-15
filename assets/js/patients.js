/**
 * assets/js/patients.js
 * Frontend AJAX สำหรับหน้าจัดการผู้ป่วย
 */

// ============================================
// ตัวแปร
// ============================================
const API_URL = 'api/patients.php';
let patientModal = null;

// ============================================
// เริ่มต้น
// ============================================
$(document).ready(function () {
    patientModal = new bootstrap.Modal(document.getElementById('patientModal'));
    
    loadPatients();
    
    // ปุ่มเพิ่ม
    $('#btnAddPatient').on('click', openAddModal);
    
    // ค้นหา (debounce 400ms)
    $('#searchInput').on('input', debounce(function () {
        const q = $(this).val().trim();
        if (q === '') {
            loadPatients();
        } else {
            searchPatients(q);
        }
    }, 400));
    
    // Submit form
    $('#patientForm').on('submit', handleSubmit);
    
    // Reset modal เมื่อปิด
    $('#patientModal').on('hidden.bs.modal', resetForm);
});

// ============================================
// โหลด / ค้นหา
// ============================================

function loadPatients() {
    showTableLoading();
    
    apiCall(API_URL + '?action=list', {}, 'GET')
        .then(res => {
            renderPatients(res.data);
            $('#resultCount').text(`พบทั้งหมด ${res.data.length} รายการ`);
        })
        .catch(err => {
            showTableError(err.message);
        });
}

function searchPatients(q) {
    showTableLoading();
    
    apiCall(API_URL + '?action=search&q=' + encodeURIComponent(q), {}, 'GET')
        .then(res => {
            renderPatients(res.data);
            $('#resultCount').text(`พบ ${res.data.length} รายการสำหรับ "${q}"`);
        })
        .catch(err => {
            showTableError(err.message);
        });
}

// ============================================
// Render
// ============================================

function renderPatients(patients) {
    const $tbody = $('#patientsTable');
    
    if (patients.length === 0) {
        $tbody.html(`
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>ไม่พบข้อมูลผู้ป่วย</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }
    
    const rows = patients.map(p => `
        <tr>
            <td><span class="badge bg-secondary">${escapeHtml(p.hn)}</span></td>
            <td>${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)}</td>
            <td>${escapeHtml(formatPhoneDisplay(p.phone))}</td>
            <td>${formatDate(p.birth_date)}</td>
            <td>${formatDate(p.registered_at)}</td>
            <td class="text-end">
                <button class="btn btn-sm btn-outline-primary btn-icon" 
                        onclick="openEditModal(${p.id})"
                        title="แก้ไข">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger btn-icon"
                        onclick="deletePatient(${p.id}, '${escapeHtml(p.first_name)} ${escapeHtml(p.last_name)}')"
                        title="ลบ">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
    
    $tbody.html(rows);
}

function showTableLoading() {
    $('#patientsTable').html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border spinner-border-sm text-primary"></div>
                กำลังโหลด...
            </td>
        </tr>
    `);
}

function showTableError(message) {
    $('#patientsTable').html(`
        <tr>
            <td colspan="6" class="text-center py-4 text-danger">
                <i class="bi bi-exclamation-triangle"></i>
                ${escapeHtml(message)}
            </td>
        </tr>
    `);
}

// ============================================
// Modal
// ============================================

function openAddModal() {
    resetForm();
    $('#modalTitle').text('เพิ่มผู้ป่วยใหม่');
    $('#hnDisplay').addClass('d-none');
    patientModal.show();
}

function openEditModal(id) {
    showSwalLoading('กำลังโหลดข้อมูล...');
    
    apiCall(API_URL + '?action=get&id=' + id, {}, 'GET')
        .then(res => {
            const p = res.data;
            $('#patientId').val(p.id);
            $('#firstName').val(p.first_name);
            $('#lastName').val(p.last_name);
            $('#phone').val(p.phone);
            $('#birthDate').val(p.birth_date);
            
            $('#modalTitle').text('แก้ไขข้อมูลผู้ป่วย');
            $('#hnValue').text(p.hn);
            $('#hnDisplay').removeClass('d-none');
            
            closeSwalLoading();
            patientModal.show();
        })
        .catch(err => {
            closeSwalLoading();
            alertError('โหลดไม่สำเร็จ', err.message);
        });
}

function resetForm() {
    $('#patientForm')[0].reset();
    $('#patientId').val('');
}

// ============================================
// Submit
// ============================================

function handleSubmit(e) {
    e.preventDefault();
    
    const id = $('#patientId').val();
    const isEdit = id !== '';
    
    const data = {
        action: isEdit ? 'update' : 'create',
        first_name: $('#firstName').val().trim(),
        last_name: $('#lastName').val().trim(),
        phone: $('#phone').val().trim(),
        birth_date: $('#birthDate').val()
    };
    
    if (isEdit) data.id = parseInt(id);
    
    // Disable ปุ่ม
    const $btn = $('#btnSave');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> กำลังบันทึก...');
    
    apiCall(API_URL, data)
        .then(res => {
            patientModal.hide();
            showToast(isEdit ? 'แก้ไขผู้ป่วยสำเร็จ' : `เพิ่มผู้ป่วยสำเร็จ (HN: ${res.data.hn})`, 'success');
            loadPatients();
        })
        .catch(err => {
            alertError('บันทึกไม่สำเร็จ', err.message);
        })
        .finally(() => {
            $btn.prop('disabled', false).html(originalHtml);
        });
}

// ============================================
// ลบ
// ============================================

async function deletePatient(id, name) {
    const confirmed = await confirmDialog(
        'ยืนยันการลบ?',
        `คุณต้องการลบผู้ป่วย "${name}" ใช่หรือไม่?\nการลบไม่สามารถย้อนกลับได้`,
        'ลบผู้ป่วย'
    );
    
    if (!confirmed) return;
    
    try {
        await apiCall(API_URL, { action: 'delete', id: id });
        showToast('ลบผู้ป่วยสำเร็จ', 'success');
        loadPatients();
    } catch (err) {
        alertError('ลบไม่สำเร็จ', err.message);
    }
}