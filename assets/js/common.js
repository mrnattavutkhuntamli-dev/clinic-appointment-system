// ============================================
// Toast แจ้งเตือน (Bootstrap)
// ============================================
function showToast(message, type = 'success', duration = 3000) {
    const icons = {
        success: 'check-circle-fill',
        error: 'x-circle-fill',
        warning: 'exclamation-triangle-fill',
        info: 'info-circle-fill'
    };
    
    const colors = {
        success: 'text-success',
        error: 'text-danger',
        warning: 'text-warning',
        info: 'text-info'
    };
    
    const $toast = $(`
        <div class="toast align-items-center border-0 shadow-sm" role="alert">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="bi bi-${icons[type]} ${colors[type]} me-2 fs-5"></i>
                    <span>${message}</span>
                </div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    if (!$('.toast-container-custom').length) {
        $('body').append('<div class="toast-container-custom"></div>');
    }
    
    $('.toast-container-custom').append($toast);
    
    const toast = new bootstrap.Toast($toast[0], { delay: duration });
    toast.show();
    
    $toast.on('hidden.bs.toast', function () {
        $(this).remove();
    });
}

// ============================================
// SweetAlert2 Dialogs
// ============================================
function confirmDialog(title, text = '', confirmText = 'ยืนยัน') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#dc3545',  // แดง
        cancelButtonColor: '#6c757d',    // เทา
        reverseButtons: true
    }).then(result => result.isConfirmed);
}

function alertDialog(title, text = '', icon = 'info') {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#0d6efd'
    });
}

function alertSuccess(title, text = '') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function alertError(title, text = '') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'error',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#dc3545'
    });
}

function showSwalLoading(title = 'กำลังโหลด...') {
    Swal.fire({
        title: title,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function closeSwalLoading() {
    Swal.close();
}

function apiCall(url, data = {}, method = 'POST') {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: method,
            data: method === 'GET' ? data : JSON.stringify(data),
            contentType: method === 'POST' ? 'application/json' : undefined,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    resolve(response);
                } else {
                    reject(new Error(response.error || 'เกิดข้อผิดพลาด'));
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                reject(new Error('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'));
            }
        });
    });
}

function showLoading() {
    if (!$('.loading-overlay').length) {
        $('body').append(`
            <div class="loading-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
    }
    $('.loading-overlay').addClass('show');
}

function hideLoading() {
    $('.loading-overlay').removeClass('show');
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function debounce(func, wait = 500) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

function formatTime(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${hours}:${minutes}`;
}

function formatPhoneDisplay(phone) {
    if (!phone || phone.length !== 10) {
        return phone || '';
    }
    return phone.substr(0, 3) + '-' 
         + phone.substr(3, 3) + '-' 
         + phone.substr(6, 4);
}