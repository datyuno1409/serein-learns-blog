// Posts Management JavaScript

class PostsManager {
  constructor() {
    this.selectedPosts = new Set();
    this.bulkActionsBar = document.getElementById('bulkActionsBar');
    this.deleteModal = document.getElementById('deleteModal');
    this.init();
  }

  init() {
    this.bindEvents();
    this.setupDropdowns();
    this.setupAlerts();
    this.setupKeyboardShortcuts();
  }

  bindEvents() {
    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('change', (e) => {
        this.toggleAllPosts(e.target.checked);
      });
    }

    // Individual post checkboxes
    document.querySelectorAll('.post-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', (e) => {
        this.togglePost(e.target.value, e.target.checked);
      });
    });

    // Close modal when clicking outside
    if (this.deleteModal) {
      this.deleteModal.addEventListener('click', (e) => {
        if (e.target === this.deleteModal) {
          this.closeDeleteModal();
        }
      });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.status-dropdown')) {
        this.closeAllStatusDropdowns();
      }
      if (!e.target.closest('.actions-menu')) {
        this.closeAllActionsDropdowns();
      }
    });

    // Form submissions
    this.setupFormSubmissions();
  }

  setupDropdowns() {
    // Status dropdowns
    document.querySelectorAll('.status-badge').forEach(badge => {
      badge.addEventListener('click', (e) => {
        e.stopPropagation();
        const postId = e.target.closest('[onclick]').getAttribute('onclick').match(/\d+/)[0];
        this.toggleStatusDropdown(postId);
      });
    });

    // Actions dropdowns
    document.querySelectorAll('.actions-trigger').forEach(trigger => {
      trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        const postId = e.target.closest('[onclick]').getAttribute('onclick').match(/\d+/)[0];
        this.toggleActionsMenu(postId);
      });
    });
  }

  setupAlerts() {
    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
      setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => {
          alert.remove();
        }, 300);
      }, 5000);
    });
  }

  setupKeyboardShortcuts() {
    document.addEventListener('keydown', (e) => {
      // Ctrl/Cmd + A to select all
      if ((e.ctrlKey || e.metaKey) && e.key === 'a' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
          selectAllCheckbox.checked = true;
          this.toggleAllPosts(true);
        }
      }

      // Escape to close modals and dropdowns
      if (e.key === 'Escape') {
        this.closeDeleteModal();
        this.closeAllStatusDropdowns();
        this.closeAllActionsDropdowns();
        this.closeBulkActions();
      }

      // Delete key for bulk delete
      if (e.key === 'Delete' && this.selectedPosts.size > 0) {
        this.bulkDelete();
      }
    });
  }

  setupFormSubmissions() {
    // Status update forms
    document.querySelectorAll('.status-menu form').forEach(form => {
      form.addEventListener('submit', (e) => {
        const button = e.submitter;
        const status = button.value;
        const postId = form.querySelector('input[name="post_id"]').value;
        
        // Add loading state
        button.disabled = true;
        button.innerHTML = '<span class="loading-spinner"></span> Đang cập nhật...';
        
        // Form will submit normally, but we show loading state
      });
    });

    // Delete form
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) {
      deleteForm.addEventListener('submit', (e) => {
        const submitButton = deleteForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="loading-spinner"></span> Đang xóa...';
      });
    }
  }

  // Post selection methods
  toggleAllPosts(checked = null) {
    const checkboxes = document.querySelectorAll('.post-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    
    if (checked === null) {
      checked = selectAllCheckbox ? selectAllCheckbox.checked : false;
    }

    checkboxes.forEach(checkbox => {
      checkbox.checked = checked;
      this.togglePost(checkbox.value, checked);
    });

    if (selectAllCheckbox) {
      selectAllCheckbox.checked = checked;
    }
  }

  togglePost(postId, checked) {
    if (checked) {
      this.selectedPosts.add(postId);
    } else {
      this.selectedPosts.delete(postId);
    }

    this.updateBulkActionsBar();
    this.updateSelectAllState();
  }

  updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const totalCheckboxes = document.querySelectorAll('.post-checkbox').length;
    
    if (selectAllCheckbox) {
      if (this.selectedPosts.size === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
      } else if (this.selectedPosts.size === totalCheckboxes) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
      } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
      }
    }
  }

  updateBulkActionsBar() {
    const count = this.selectedPosts.size;
    const countElement = this.bulkActionsBar?.querySelector('.selected-count');
    
    if (countElement) {
      countElement.textContent = `${count} bài viết được chọn`;
    }

    if (count > 0) {
      this.showBulkActions();
    } else {
      this.hideBulkActions();
    }
  }

  showBulkActions() {
    if (this.bulkActionsBar) {
      this.bulkActionsBar.classList.add('show');
    }
  }

  hideBulkActions() {
    if (this.bulkActionsBar) {
      this.bulkActionsBar.classList.remove('show');
    }
  }

  closeBulkActions() {
    this.selectedPosts.clear();
    document.querySelectorAll('.post-checkbox').forEach(checkbox => {
      checkbox.checked = false;
    });
    this.updateSelectAllState();
    this.hideBulkActions();
  }

  // Dropdown methods
  toggleStatusDropdown(postId) {
    this.closeAllStatusDropdowns();
    const menu = document.getElementById(`statusMenu${postId}`);
    if (menu) {
      menu.classList.add('show');
    }
  }

  closeAllStatusDropdowns() {
    document.querySelectorAll('.status-menu').forEach(menu => {
      menu.classList.remove('show');
    });
  }

  toggleActionsMenu(postId) {
    this.closeAllActionsDropdowns();
    const menu = document.getElementById(`actionsMenu${postId}`);
    if (menu) {
      menu.classList.add('show');
    }
  }

  closeAllActionsDropdowns() {
    document.querySelectorAll('.actions-dropdown').forEach(menu => {
      menu.classList.remove('show');
    });
  }

  // Modal methods
  confirmDelete(postId, postTitle) {
    const modal = document.getElementById('deleteModal');
    const titleElement = document.getElementById('deletePostTitle');
    const idInput = document.getElementById('deletePostId');
    
    if (modal && titleElement && idInput) {
      titleElement.textContent = postTitle;
      idInput.value = postId;
      modal.classList.add('show');
      
      // Focus on cancel button for accessibility
      setTimeout(() => {
        const cancelButton = modal.querySelector('.btn-secondary');
        if (cancelButton) {
          cancelButton.focus();
        }
      }, 100);
    }
  }

  closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
      modal.classList.remove('show');
    }
  }

  // Bulk actions
  bulkUpdateStatus(status) {
    if (this.selectedPosts.size === 0) {
      this.showNotification('Vui lòng chọn ít nhất một bài viết', 'warning');
      return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/posts/bulk-update-status';
    
    // Add CSRF token if available
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = csrfToken.content;
      form.appendChild(csrfInput);
    }

    // Add selected post IDs
    this.selectedPosts.forEach(postId => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'post_ids[]';
      input.value = postId;
      form.appendChild(input);
    });

    // Add status
    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = status;
    form.appendChild(statusInput);

    document.body.appendChild(form);
    form.submit();
  }

  bulkDelete() {
    if (this.selectedPosts.size === 0) {
      this.showNotification('Vui lòng chọn ít nhất một bài viết', 'warning');
      return;
    }

    const count = this.selectedPosts.size;
    const message = `Bạn có chắc chắn muốn xóa ${count} bài viết đã chọn?`;
    
    if (confirm(message)) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/admin/posts/bulk-delete';
      
      // Add CSRF token if available
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.content;
        form.appendChild(csrfInput);
      }

      // Add selected post IDs
      this.selectedPosts.forEach(postId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'post_ids[]';
        input.value = postId;
        form.appendChild(input);
      });

      document.body.appendChild(form);
      form.submit();
    }
  }

  // Utility methods
  showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.innerHTML = `
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
      </svg>
      ${message}
    `;

    const container = document.querySelector('.posts-content');
    if (container) {
      container.insertBefore(notification, container.firstChild);
      
      // Auto-hide after 3 seconds
      setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
          notification.remove();
        }, 300);
      }, 3000);
    }
  }

  // Search and filter helpers
  setupLiveSearch() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
      let searchTimeout;
      
      searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          // Auto-submit form after 500ms of no typing
          const form = e.target.closest('form');
          if (form && e.target.value.length >= 3) {
            form.submit();
          }
        }, 500);
      });
    }
  }

  // Loading states
  showLoading(element) {
    if (element) {
      element.disabled = true;
      element.classList.add('loading');
      
      const originalText = element.textContent;
      element.dataset.originalText = originalText;
      element.innerHTML = '<span class="loading-spinner"></span> Đang xử lý...';
    }
  }

  hideLoading(element) {
    if (element) {
      element.disabled = false;
      element.classList.remove('loading');
      
      const originalText = element.dataset.originalText;
      if (originalText) {
        element.textContent = originalText;
        delete element.dataset.originalText;
      }
    }
  }
}

// Global functions for inline event handlers (backward compatibility)
function toggleBulkActions() {
  if (window.postsManager) {
    const bulkBar = document.getElementById('bulkActionsBar');
    if (bulkBar.classList.contains('show')) {
      window.postsManager.closeBulkActions();
    } else {
      // Select all posts to show bulk actions
      window.postsManager.toggleAllPosts(true);
    }
  }
}

function toggleAllPosts() {
  if (window.postsManager) {
    window.postsManager.toggleAllPosts();
  }
}

function toggleStatusDropdown(postId) {
  if (window.postsManager) {
    window.postsManager.toggleStatusDropdown(postId);
  }
}

function toggleActionsMenu(postId) {
  if (window.postsManager) {
    window.postsManager.toggleActionsMenu(postId);
  }
}

function confirmDelete(postId, postTitle) {
  if (window.postsManager) {
    window.postsManager.confirmDelete(postId, postTitle);
  }
}

function closeDeleteModal() {
  if (window.postsManager) {
    window.postsManager.closeDeleteModal();
  }
}

function bulkUpdateStatus(status) {
  if (window.postsManager) {
    window.postsManager.bulkUpdateStatus(status);
  }
}

function bulkDelete() {
  if (window.postsManager) {
    window.postsManager.bulkDelete();
  }
}

function closeBulkActions() {
  if (window.postsManager) {
    window.postsManager.closeBulkActions();
  }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  window.postsManager = new PostsManager();
  
  // Setup live search if enabled
  if (document.querySelector('.live-search-enabled')) {
    window.postsManager.setupLiveSearch();
  }
});

// Add loading spinner CSS
const style = document.createElement('style');
style.textContent = `
  .loading-spinner {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  .loading {
    opacity: 0.7;
    pointer-events: none;
  }
`;
document.head.appendChild(style);