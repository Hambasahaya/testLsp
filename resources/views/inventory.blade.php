@extends('layouts.app')

@section('title', 'Inventaris')
@section('page-title', 'Manajemen Inventaris')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        @if(auth()->user()->hasRole('Admin'))
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="fas fa-plus"></i> Tambah Produk Baru
        </button>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Produk</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="products-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="products-tbody">
                    <tr>
                        <td colspan="5" class="text-center">
                            <span class="spinner-border spinner-border-sm"></span> Loading...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="add-product-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Jual Produk -->
<div class="modal fade" id="sellProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Jual Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sell-product-form">
                <div class="modal-body">
                    <p><strong>Produk:</strong> <span id="sell-product-name"></span></p>
                    <p><strong>Stok Tersedia:</strong> <span id="sell-product-stock"></span></p>
                    <div class="mb-3">
                        <label for="sell-quantity" class="form-label">Jumlah Penjualan</label>
                        <input type="number" class="form-control" id="sell-quantity" name="quantity" min="1" required>
                    </div>
                    <p class="text-muted" id="sell-validation-msg"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Jual</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Produk -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-product-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit-description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-price" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="edit-price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-stock" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="edit-stock" name="stock" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    let currentSellProductId = null;
    let currentSellProductStock = 0;

    async function loadProducts() {
        try {
            const response = await fetchWithAuth('/api/products');

            if (response.ok) {
                const data = await response.json();
                const products = data.data;
                const tbody = document.getElementById('products-tbody');

                if (products.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada produk</td></tr>';
                    return;
                }

                tbody.innerHTML = products.map(product => `
                    <tr>
                        <td><strong>${escapeHtml(product.name)}</strong></td>
                        <td>${escapeHtml(product.description || '-')}</td>
                        <td>Rp ${parseFloat(product.price).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                        <td>
                            <span class="badge ${product.stock < 10 ? 'bg-danger' : 'bg-success'}">
                                ${product.stock} unit
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                ${shouldShowSellButton() ? `
                                    <button type="button" class="btn btn-sm btn-warning" 
                                            onclick="openSellModal(${product.id}, '${escapeHtml(product.name)}', ${product.stock})">
                                        <i class="fas fa-shopping-cart"></i> Jual
                                    </button>
                                ` : ''}
                                ${shouldShowEditButton() ? `
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="openEditModal(${product.id}, '${escapeHtml(product.name)}', '${escapeHtml(product.description || '')}', ${product.price}, ${product.stock})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteProduct(${product.id}, '${escapeHtml(product.name)}')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `).join('');
            } else {
                document.getElementById('products-tbody').innerHTML =
                    '<tr><td colspan="5" class="text-center text-danger">Error loading products</td></tr>';
            }
        } catch (error) {
            console.error('Error loading products:', error);
            document.getElementById('products-tbody').innerHTML =
                '<tr><td colspan="5" class="text-center text-danger">Error: ' + error.message + '</td></tr>';
        }
    }

    function shouldShowSellButton() {
        const role = '{{ auth()->user()->role->name }}';
        return ['Admin', 'Seller'].includes(role);
    }

    function shouldShowEditButton() {
        const role = '{{ auth()->user()->role->name }}';
        return role === 'Admin';
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }

    function openSellModal(productId, productName, stock) {
        currentSellProductId = productId;
        currentSellProductStock = stock;
        document.getElementById('sell-product-name').textContent = productName;
        document.getElementById('sell-product-stock').textContent = stock;
        document.getElementById('sell-quantity').value = '';
        document.getElementById('sell-validation-msg').textContent = '';

        const modal = new bootstrap.Modal(document.getElementById('sellProductModal'));
        modal.show();
    }

    function openEditModal(productId, name, description, price, stock) {
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-stock').value = stock;
        
        // Store ID for later use in form submission
        document.getElementById('edit-product-form').dataset.productId = productId;

        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
    }

    function deleteProduct(productId, productName) {
        if (!confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?`)) {
            return;
        }

        // For now, we'll just show an alert since delete endpoint not yet created
        showAlert('Fitur delete akan ditambahkan di versi berikutnya', 'info');
    }

    document.getElementById('sell-quantity').addEventListener('input', function() {
        const quantity = parseInt(this.value) || 0;
        const msg = document.getElementById('sell-validation-msg');

        if (quantity > currentSellProductStock) {
            msg.classList.add('text-danger');
            msg.classList.remove('text-success');
            msg.textContent = `⚠️ Stok tidak cukup! Hanya tersedia ${currentSellProductStock} unit`;
        } else if (quantity > 0) {
            msg.classList.remove('text-danger');
            msg.classList.add('text-success');
            msg.textContent = `✓ Siap untuk dijual`;
        } else {
            msg.textContent = '';
        }
    });

    document.getElementById('add-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        try {
            const response = await fetchWithAuth('/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: document.getElementById('name').value,
                    description: document.getElementById('description').value,
                    price: document.getElementById('price').value,
                    stock: document.getElementById('stock').value
                })
            });

            if (response.ok) {
                showAlert('Produk berhasil ditambahkan', 'success');
                this.reset();
                loadProducts();
            } else {
                const error = await response.json();
                showAlert(error.message || 'Error adding product', 'danger');
            }
        } catch (error) {
            showAlert('Error: ' + error.message, 'danger');
        }
    });

    document.getElementById('sell-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const quantity = parseInt(document.getElementById('sell-quantity').value);

        if (quantity > currentSellProductStock) {
            showAlert('Stok tidak cukup!', 'danger');
            return;
        }

        try {
            const response = await fetchWithAuth(`/api/products/${currentSellProductId}/sell`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    quantity: quantity
                })
            });

            if (response.ok) {
                bootstrap.Modal.getInstance(document.getElementById('sellProductModal')).hide();
                showAlert('Penjualan berhasil dicatat', 'success');
                loadProducts();
            } else {
                const error = await response.json();
                showAlert(error.message || 'Error processing sale', 'danger');
            }
        } catch (error) {
            showAlert('Error: ' + error.message, 'danger');
        }
    });

    document.getElementById('edit-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const productId = this.dataset.productId;
        try {
            const response = await fetchWithAuth(`/api/products/${productId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: document.getElementById('edit-name').value,
                    description: document.getElementById('edit-description').value,
                    price: document.getElementById('edit-price').value,
                    stock: document.getElementById('edit-stock').value
                })
            });

            if (response.ok) {
                bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                showAlert('Produk berhasil diperbarui', 'success');
                loadProducts();
            } else {
                const error = await response.json();
                showAlert(error.message || 'Error updating product', 'danger');
            }
        } catch (error) {
            showAlert('Error: ' + error.message, 'danger');
        }
    });

    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        const mainContent = document.querySelector('.main-content');
        const navbar = mainContent.querySelector('.navbar');
        const div = document.createElement('div');
        div.innerHTML = alertHtml;
        navbar.parentElement.insertBefore(div.firstElementChild, navbar.nextSibling);
    }

    // Load products on page load
    loadProducts();
</script>
@endsection