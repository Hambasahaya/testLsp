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

<!-- Period Filter untuk Charts -->
<div class="card mb-3">
    <div class="card-body">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSalesStats('day')">
                <i class="fas fa-calendar-day"></i> Hari Ini
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSalesStats('week')">
                <i class="fas fa-calendar-week"></i> Minggu Ini
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSalesStats('month')">
                <i class="fas fa-calendar-alt"></i> Bulan Ini
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSalesStats('year')">
                <i class="fas fa-calendar"></i> Tahun Ini
            </button>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistik Penjualan</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Produk Terlaris</h5>
            </div>
            <div class="card-body">
                <canvas id="topProductsChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Total Transaksi</p>
                <h4 id="stat-transactions">0</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Total Terjual</p>
                <h4 id="stat-quantity">0</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Total Revenue</p>
                <h4 id="stat-revenue">Rp 0</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Rata-rata per Transaksi</p>
                <h4 id="stat-average">Rp 0</h4>
            </div>
        </div>
    </div>
</div>

<!-- Top Products Table -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Produk Terlaris Detail</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="top-products-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Transaksi</th>
                        <th>Total Terjual</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody id="top-products-tbody">
                    <tr>
                        <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                </tbody>
            </table>
        </div>
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
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="products-tbody">
                    <tr>
                        <td colspan="6" class="text-center">
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
            <form id="add-product-form" enctype="multipart/form-data">
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
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control price-input" id="price" name="price" required placeholder="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">Foto Produk</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF (Maks 2MB)</small>
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
                    <button type="submit" class="btn btn-primary" id="sell-submit-btn" disabled>
                        <i class="fas fa-check"></i> Jual
                    </button>
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
            <form id="edit-product-form" enctype="multipart/form-data">
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
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control price-input" id="edit-price" name="price" required placeholder="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-stock" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="edit-stock" name="stock" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-photo" class="form-label">Foto Produk</label>
                        <input type="file" class="form-control" id="edit-photo" name="photo" accept="image/*">
                        <small class="text-muted">Format: JPEG, PNG, JPG, GIF (Maks 2MB). Biarkan kosong jika tidak ingin mengubah.</small>
                        <div id="edit-photo-preview" class="mt-2"></div>
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
    let salesChart = null;
    let topProductsChart = null;
    let currentPeriod = 'day';
    let autoRefreshInterval = null;

    // Show loading state
    function showLoading(elementId, show = true) {
        const element = document.getElementById(elementId);
        if (!element) return;

        if (show) {
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
        } else {
            element.style.opacity = '1';
            element.style.pointerEvents = 'auto';
        }
    }

    // Rupiah formatting functions
    function formatRupiah(value) {
        if (!value) return '';

        // Remove non-digit characters
        const numericValue = value.toString().replace(/\D/g, '');
        if (!numericValue) return '';

        // Format with thousand separator
        return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function getRupiahValue(formattedValue) {
        // Remove all dots and return the raw number
        return formattedValue.toString().replace(/\./g, '');
    }

    // Add event listeners for price inputs
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('price-input')) {
            const cursorPosition = e.target.selectionStart;
            const oldValue = e.target.value;
            const newValue = formatRupiah(e.target.value);

            e.target.value = newValue;

            // Adjust cursor position based on formatting
            const diff = newValue.length - oldValue.length;
            e.target.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        }
    });

    async function loadSalesStats(period = 'day') {
        currentPeriod = period;
        showLoading('salesChart', true);
        showLoading('topProductsChart', true);

        try {
            const response = await fetchWithAuth(`/api/products/stats/sales?period=${period}`);

            if (response.ok) {
                const data = await response.json();
                updateCharts(data.data);
                updateStatsCards(data.data);
                updateTopProductsTable(data.data.top_products);
            } else {
                showAlert('Error loading sales statistics', 'danger');
            }
        } catch (error) {
            console.error('Error loading sales stats:', error);
            showAlert('Error: ' + error.message, 'danger');
        } finally {
            showLoading('salesChart', false);
            showLoading('topProductsChart', false);
        }
    }

    function updateCharts(statsData) {
        const labels = statsData.stats.map(s => s.time_period);
        const quantities = statsData.stats.map(s => parseInt(s.total_quantity) || 0);
        const revenues = statsData.stats.map(s => parseFloat(s.total_revenue) || 0);

        // Destroy old charts if they exist
        if (salesChart) salesChart.destroy();
        if (topProductsChart) topProductsChart.destroy();

        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Total Terjual (unit)',
                        data: quantities,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue (Rp)',
                        data: revenues,
                        borderColor: '#764ba2',
                        backgroundColor: 'rgba(118, 75, 162, 0.1)',
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Quantity (unit)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue (Rp)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });

        // Top Products Chart
        const topProducts = statsData.top_products.slice(0, 5);
        const productLabels = topProducts.map(p => p.product.name);
        const productQuantities = topProducts.map(p => parseInt(p.total_quantity) || 0);
        const productRevenues = topProducts.map(p => parseFloat(p.total_revenue) || 0);

        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        topProductsChart = new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: productLabels,
                datasets: [{
                        label: 'Terjual (unit)',
                        data: productQuantities,
                        backgroundColor: '#667eea',
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue (Rp)',
                        data: productRevenues,
                        backgroundColor: '#764ba2',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Quantity (unit)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue (Rp)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    }

    function updateStatsCards(statsData) {
        const totalTransactions = statsData.total_transactions || 0;
        const totalQuantity = statsData.total_quantity || 0;
        const totalRevenue = statsData.total_revenue || 0;
        const average = totalTransactions > 0 ? totalRevenue / totalTransactions : 0;

        document.getElementById('stat-transactions').textContent = totalTransactions.toLocaleString('id-ID');
        document.getElementById('stat-quantity').textContent = totalQuantity.toLocaleString('id-ID');
        document.getElementById('stat-revenue').textContent = 'Rp ' + totalRevenue.toLocaleString('id-ID', {
            minimumFractionDigits: 2
        });
        document.getElementById('stat-average').textContent = 'Rp ' + average.toLocaleString('id-ID', {
            minimumFractionDigits: 2
        });
    }

    function updateTopProductsTable(topProducts) {
        const tbody = document.getElementById('top-products-tbody');

        if (topProducts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Tidak ada data</td></tr>';
            return;
        }

        tbody.innerHTML = topProducts.map(product => `
            <tr>
                <td><strong>${escapeHtml(product.product.name)}</strong></td>
                <td>${product.transaction_count}</td>
                <td>${product.total_quantity} unit</td>
                <td>Rp ${parseFloat(product.total_revenue).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
            </tr>
        `).join('');
    }

    async function loadProducts() {
        showLoading('products-tbody', true);
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
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                ${product.photo ? `<img src="/storage/${product.photo}" alt="${escapeHtml(product.name)}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">` : `<div class="rounded bg-secondary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-image text-white"></i></div>`}
                                <strong>${escapeHtml(product.name)}</strong>
                            </div>
                        </td>
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
                                            onclick="openSellModal(${product.id}, '${escapeHtml(product.name)}', ${product.stock})"
                                            ${product.stock < 1 ? 'disabled' : ''}
                                            title="${product.stock < 1 ? 'Stok tidak tersedia' : ''}">
                                        <i class="fas fa-shopping-cart"></i> Jual
                                    </button>
                                ` : ''}
                                ${shouldShowEditButton() ? `
                                    <button type="button" class="btn btn-sm btn-info" 
                                            onclick="openEditModal(${product.id}, '${escapeHtml(product.name)}', '${escapeHtml(product.description || '')}', ${product.price}, ${product.stock}, '${product.photo || ''}')" > 
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
                showAlert('Error loading products', 'danger');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            showAlert('Error: ' + error.message, 'danger');
        } finally {
            showLoading('products-tbody', false);
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

    function openEditModal(productId, name, description, price, stock, photo) {
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-price').value = price;
        document.getElementById('edit-stock').value = stock;
        document.getElementById('edit-photo').value = '';

        // Show current photo preview if exists
        const previewDiv = document.getElementById('edit-photo-preview');
        if (photo) {
            previewDiv.innerHTML = `<img src="/storage/${photo}" alt="${escapeHtml(name)}" class="img-thumbnail" style="max-width: 150px;">`;
        } else {
            previewDiv.innerHTML = '';
        }

        // Store ID for later use in form submission
        document.getElementById('edit-product-form').dataset.productId = productId;

        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
    }

    function deleteProduct(productId, productName) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus produk "<strong>${escapeHtml(productName)}</strong>"?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                performDelete(productId, productName);
            }
        });
    }

    async function performDelete(productId, productName) {
        try {
            const response = await fetchWithAuth(`/api/products/${productId}`, {
                method: 'DELETE'
            });

            if (response.ok) {
                showAlert(`Produk "${productName}" berhasil dihapus`, 'success');
                loadProducts();
                loadSalesStats(currentPeriod);
            } else {
                const error = await response.json();
                showAlert(error.message || 'Error menghapus produk', 'danger');
            }
        } catch (error) {
            console.error('Error deleting product:', error);
            showAlert('Error: ' + error.message, 'danger');
        }
    }

    document.getElementById('sell-quantity').addEventListener('input', function() {
        const quantity = parseInt(this.value) || 0;
        const msg = document.getElementById('sell-validation-msg');
        const submitBtn = document.getElementById('sell-submit-btn');

        if (quantity > currentSellProductStock) {
            msg.classList.add('text-danger');
            msg.classList.remove('text-success');
            msg.textContent = `⚠️ Stok tidak cukup! Hanya tersedia ${currentSellProductStock} unit`;
            submitBtn.disabled = true;
        } else if (quantity > 0) {
            msg.classList.remove('text-danger');
            msg.classList.add('text-success');
            msg.textContent = `✓ Siap untuk dijual`;
            submitBtn.disabled = false;
        } else {
            msg.textContent = '';
            submitBtn.disabled = true;
        }
    });

    document.getElementById('add-product-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        // Convert Rupiah formatted price back to numeric
        const priceValue = document.getElementById('price').value;
        formData.set('price', getRupiahValue(priceValue));

        try {
            const response = await fetchWithAuth('/api/products', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                showAlert('Produk berhasil ditambahkan', 'success');
                this.reset();
                bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                loadProducts();
                loadSalesStats(currentPeriod);
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
                loadSalesStats(currentPeriod);
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
        const formData = new FormData(this);
        // Convert Rupiah formatted price back to numeric
        const priceValue = document.getElementById('edit-price').value;
        formData.set('price', getRupiahValue(priceValue));

        try {
            const response = await fetchWithAuth(`/api/products/${productId}`, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
                showAlert('Produk berhasil diperbarui', 'success');
                loadProducts();
                loadSalesStats(currentPeriod);
            } else {
                const error = await response.json();
                showAlert(error.message || 'Error updating product', 'danger');
            }
        } catch (error) {
            showAlert('Error: ' + error.message, 'danger');
        }
    });

    function showAlert(message, type) {
        const typeMap = {
            'success': 'success',
            'danger': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        Swal.fire({
            icon: typeMap[type] || 'info',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Load products and stats on page load
    loadProducts();
    loadSalesStats('day');

    // Refresh stats and products every 5 seconds for real-time updates
    autoRefreshInterval = setInterval(() => {
        loadSalesStats(currentPeriod);
        loadProducts();
    }, 5000);
</script>
@endsection