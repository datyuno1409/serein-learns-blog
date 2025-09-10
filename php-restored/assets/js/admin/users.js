document.addEventListener('DOMContentLoaded', function() {
    initializeUserManagement();
});

function initializeUserManagement() {
    initializeCheckboxes();
    initializeBulkActions();
    initializeModals();
}

function initializeCheckboxes() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkActionsBar = document.querySelector('.bulk-actions-bar');
    const bulkActions = document.querySelector('.bulk-actions');
    const selectedCountElements = document.querySelectorAll('.selected-count');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsVisibility();
        });
    }

    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkActionsVisibility();
        });
    });

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const totalBoxes = userCheckboxes.length;
        
        if (selectAllCheckbox) {
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedBoxes.length === totalBoxes) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
            }
        }
    }

    function updateBulkActionsVisibility() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCountElements.forEach(element => {
            element.textContent = `${count} người dùng được chọn`;
        });

        if (count > 0) {
            if (bulkActionsBar) bulkActionsBar.style.display = 'flex';
            if (bulkActions) bulkActions.style.display = 'flex';
        } else {
            if (bulkActionsBar) bulkActionsBar.style.display = 'none';
            if (bulkActions) bulkActions.style.display = 'none';
        }
    }
}

function initializeBulkActions() {
    // Bulk actions will be implemented when backend endpoints are ready
}

function initializeModals() {
    // Initialize Bootstrap modals if available
    if (typeof bootstrap !== 'undefined') {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        const userDetailsModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
    }
}

function deleteUser(userId, username) {
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    const confirmBtn = document.getElementById('confirmDelete');
    
    if (message) {
        message.textContent = `Bạn có chắc chắn muốn xóa người dùng "${username}"?`;
    }
    
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            performUserAction('delete', userId);
        };
    }
    
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else {
        modal.style.display = 'block';
    }
}

function toggleUserStatus(userId, currentStatus) {
    const modal = document.getElementById('statusModal');
    const message = document.getElementById('statusMessage');
    const confirmBtn = document.getElementById('confirmStatusChange');
    const newStatus = currentStatus == 1 ? 0 : 1;
    const actionText = newStatus == 1 ? 'kích hoạt' : 'khóa';
    
    if (message) {
        message.textContent = `Bạn có chắc chắn muốn ${actionText} tài khoản này?`;
    }
    
    if (confirmBtn) {
        confirmBtn.onclick = function() {
            performUserAction('toggle_status', userId, { new_status: newStatus });
        };
    }
    
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else {
        modal.style.display = 'block';
    }
}

function viewUserDetails(userId) {
    const modal = document.getElementById('userDetailsModal');
    const content = document.getElementById('userDetailsContent');
    
    if (content) {
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        `;
    }
    
    if (typeof bootstrap !== 'undefined') {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else {
        modal.style.display = 'block';
    }
    
    // Load user details via AJAX
    fetch(`/admin/users/details?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && content) {
                content.innerHTML = renderUserDetails(data.user);
            } else {
                if (content) {
                    content.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            Không thể tải thông tin người dùng.
                        </div>
                    `;
                }
            }
        })
        .catch(error => {
            console.error('Error loading user details:', error);
            if (content) {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        Đã xảy ra lỗi khi tải thông tin người dùng.
                    </div>
                `;
            }
        });
}

function renderUserDetails(user) {
    return `
        <div class="user-details-content">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="user-avatar-large">
                        ${user.avatar ? 
                            `<img src="${user.avatar}" alt="Avatar" class="img-fluid rounded-circle">` :
                            `<div class="avatar-placeholder-large"><i class="fas fa-user"></i></div>`
                        }
                    </div>
                    <h5 class="mt-3">${user.full_name || user.username}</h5>
                    <p class="text-muted">@${user.username}</p>
                </div>
                <div class="col-md-8">
                    <div class="user-info-grid">
                        <div class="info-item">
                            <label>Email:</label>
                            <span>${user.email}</span>
                        </div>
                        <div class="info-item">
                            <label>Vai trò:</label>
                            <span class="badge role-badge role-${user.role}">
                                <i class="fas fa-${user.role === 'admin' ? 'crown' : 'user'}"></i>
                                ${user.role === 'admin' ? 'Quản trị viên' : 'Người dùng'}
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Trạng thái:</label>
                            <span class="badge status-badge status-${user.is_active == 1 ? 'active' : 'inactive'}">
                                <i class="fas fa-${user.is_active == 1 ? 'check-circle' : 'times-circle'}"></i>
                                ${user.is_active == 1 ? 'Hoạt động' : 'Tạm khóa'}
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Ngày tạo:</label>
                            <span>${new Date(user.created_at).toLocaleDateString('vi-VN')}</span>
                        </div>
                        <div class="info-item">
                            <label>Lần đăng nhập cuối:</label>
                            <span>${user.last_login ? new Date(user.last_login).toLocaleDateString('vi-VN') : 'Chưa đăng nhập'}</span>
                        </div>
                        <div class="info-item">
                            <label>Số bài viết:</label>
                            <span class="badge bg-info">${user.post_count || 0}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function performUserAction(action, userId, extraData = {}) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('user_id', userId);
    
    Object.keys(extraData).forEach(key => {
        formData.append(key, extraData[key]);
    });
    
    fetch('/admin/users/action', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Đã xảy ra lỗi', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Đã xảy ra lỗi khi thực hiện thao tác', 'error');
    })
    .finally(() => {
        // Close all modals
        document.querySelectorAll('.modal').forEach(modal => {
            if (typeof bootstrap !== 'undefined') {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            } else {
                modal.style.display = 'none';
            }
        });
    });
}

function bulkToggleStatus() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const userIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (userIds.length === 0) {
        showNotification('Vui lòng chọn ít nhất một người dùng', 'warning');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái của ${userIds.length} người dùng?`)) {
        performBulkAction('bulk_toggle_status', userIds);
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const userIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (userIds.length === 0) {
        showNotification('Vui lòng chọn ít nhất một người dùng', 'warning');
        return;
    }
    
    if (confirm(`Bạn có chắc chắn muốn xóa ${userIds.length} người dùng? Hành động này không thể hoàn tác!`)) {
        performBulkAction('bulk_delete', userIds);
    }
}

function performBulkAction(action, userIds) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('user_ids', JSON.stringify(userIds));
    
    fetch('/admin/users/bulk-action', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Đã xảy ra lỗi', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Đã xảy ra lỗi khi thực hiện thao tác', 'error');
    });
}

function clearSelection() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
    
    document.querySelector('.bulk-actions-bar').style.display = 'none';
    document.querySelector('.bulk-actions').style.display = 'none';
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    const icon = type === 'success' ? 'check-circle' : 
                type === 'error' ? 'exclamation-circle' : 
                type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
    
    // Handle close button
    const closeBtn = notification.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            notification.remove();
        });
    }
}

// Export functions for global access
window.deleteUser = deleteUser;
window.toggleUserStatus = toggleUserStatus;
window.viewUserDetails = viewUserDetails;
window.bulkToggleStatus = bulkToggleStatus;
window.bulkDelete = bulkDelete;
window.clearSelection = clearSelection;