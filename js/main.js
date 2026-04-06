document.addEventListener('DOMContentLoaded', function () {
 
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });
 
    document.querySelectorAll('.confirm-delete').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('Apakah Anda yakin ingin menghapus item ini?\nTindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });
 
    document.querySelectorAll('.qty-input').forEach(input => {
        const min = parseInt(input.getAttribute('min')) || 1;
        const max = parseInt(input.getAttribute('max')) || 999;
 
        const btnMinus = input.previousElementSibling;
        const btnPlus = input.nextElementSibling;
 
        if (btnMinus) {
            btnMinus.addEventListener('click', () => {
                let v = parseInt(input.value);
                if (v > min) { input.value = v - 1; input.dispatchEvent(new Event('change')); }
            });
        }
        if (btnPlus) {
            btnPlus.addEventListener('click', () => {
                let v = parseInt(input.value);
                if (v < max) { input.value = v + 1; input.dispatchEvent(new Event('change')); }
            });
        }
    });
 
    const imgInput = document.getElementById('gambar');
    const imgPreview = document.getElementById('img-preview');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    imgPreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
 
    const searchInput = document.getElementById('table-search');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }
 
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            let valid = true;
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    valid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            if (!valid) {
                e.preventDefault();
                showAlert('Harap isi semua field yang wajib diisi.', 'danger');
            }
        });
    });
 
    updateCartTotal();
 
    document.querySelectorAll('.cart-qty').forEach(input => {
        input.addEventListener('change', function () {
            if (parseInt(this.value) < 1) this.value = 1;
            updateCartTotal();
        });
    });
 
});
 
function showAlert(message, type = 'info') {
    const wrapper = document.getElementById('alert-wrapper') || document.body;
    const div = document.createElement('div');
    div.className = `alert alert-${type}`;
    div.innerHTML = `<span>${message}</span>`;
    wrapper.insertBefore(div, wrapper.firstChild);
    setTimeout(() => {
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 400);
    }, 3500);
}
 
function updateCartTotal() {
    let total = 0;
    document.querySelectorAll('.cart-item-row').forEach(row => {
        const harga = parseInt(row.dataset.harga || 0);
        const qty = parseInt(row.querySelector('.cart-qty')?.value || 0);
        const subtotal = harga * qty;
        const subtotalEl = row.querySelector('.item-subtotal');
        if (subtotalEl) subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        total += subtotal;
    });
 
    const totalEl = document.getElementById('cart-total');
    if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
}
 
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('open');
}
 