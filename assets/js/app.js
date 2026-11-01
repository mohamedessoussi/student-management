/* ============================================================
   assets/js/app.js
   UI helpers: sidebar toggle, image preview, confirm delete,
   auto-dismiss alerts
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

  // ── Mobile sidebar toggle ──────────────────────────────────
  const sidebar = document.querySelector('.sidebar');
  const toggle  = document.querySelector('.mobile-toggle');
  if (toggle && sidebar) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) &&
          !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // ── Image preview before upload ────────────────────────────
  const fileInput   = document.getElementById('imageInput');
  const previewImg  = document.getElementById('imagePreview');
  if (fileInput && previewImg) {
    fileInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      // Client-side type check
      const allowed = ['image/jpeg', 'image/jpg', 'image/png'];
      if (!allowed.includes(file.type)) {
        showToast('Only JPG and PNG images are accepted.', 'danger');
        this.value = '';
        return;
      }
      // Client-side size check (2 MB)
      if (file.size > 2 * 1024 * 1024) {
        showToast('Image must not exceed 2 MB.', 'danger');
        this.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => { previewImg.src = e.target.result; };
      reader.readAsDataURL(file);
    });
  }

  // ── Delete confirmation ────────────────────────────────────
  document.querySelectorAll('.confirm-delete').forEach((btn) => {
    btn.addEventListener('click', (e) => {
      if (!confirm('Are you sure you want to delete this student? This cannot be undone.')) {
        e.preventDefault();
      }
    });
  });

  // ── Auto-dismiss alerts after 4 seconds ────────────────────
  document.querySelectorAll('.alert.auto-dismiss').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity .4s';
      el.style.opacity    = '0';
      setTimeout(() => el.remove(), 400);
    }, 4000);
  });

  // ── Search: debounce on keyup, auto-submit ─────────────────
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    let timer;
    searchInput.addEventListener('keyup', () => {
      clearTimeout(timer);
      timer = setTimeout(() => searchInput.closest('form').submit(), 500);
    });
  }

});

// ── Toast helper ─────────────────────────────────────────────
function showToast(message, type = 'info') {
  const container = document.getElementById('toast-container') ?? (() => {
    const c = document.createElement('div');
    c.id = 'toast-container';
    c.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;';
    document.body.appendChild(c);
    return c;
  })();

  const toast = document.createElement('div');
  toast.className = `alert alert-${type} d-flex align-items-center gap-2 shadow`;
  toast.style.cssText = 'min-width:260px;max-width:380px;animation:slideIn .25s ease;';
  toast.innerHTML = `<span>${message}</span>
    <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()"></button>`;
  container.appendChild(toast);

  setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 4000);
}
