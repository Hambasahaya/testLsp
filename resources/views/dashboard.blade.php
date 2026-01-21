@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-cubes fa-3x" style="color: #667eea; margin-right: 20px;"></i>
                    <div>
                        <h6 class="text-muted">Total Produk</h6>
                        <h3 id="total-products">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-shopping-cart fa-3x" style="color: #764ba2; margin-right: 20px;"></i>
                    <div>
                        <h6 class="text-muted">Total Stok</h6>
                        <h3 id="total-stock">0</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users fa-3x" style="color: #fbbf24; margin-right: 20px;"></i>
                    <div>
                        <h6 class="text-muted">Role</h6>
                        <h3>{{ auth()->user()->role->name }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Sambutan</h5>
    </div>
    <div class="card-body">
        <p>Selamat datang, <strong>{{ auth()->user()->name }}</strong>!</p>
        <p>Anda login sebagai <span class="badge badge-{{ strtolower(auth()->user()->role->name) }}">{{ auth()->user()->role->name }}</span></p>

        @if(auth()->user()->hasRole('Admin'))
        <p><i class="fas fa-star"></i> Anda memiliki akses penuh ke semua fitur aplikasi.</p>
        @elseif(auth()->user()->hasRole('Seller'))
        <p><i class="fas fa-check"></i> Anda dapat melihat produk dan melakukan transaksi penjualan.</p>
        @else
        <p><i class="fas fa-eye"></i> Anda dapat melihat daftar produk yang tersedia.</p>
        @endif
    </div>
</div>

@endsection

@section('extra-js')
<script>
    let dashboardRefreshInterval = null;

    // Show loading state
    function showDashboardLoading(show = true) {
        const productCard = document.getElementById('total-products').parentElement.parentElement;
        const stockCard = document.getElementById('total-stock').parentElement.parentElement;

        if (show) {
            productCard.style.opacity = '0.6';
            stockCard.style.opacity = '0.6';
        } else {
            productCard.style.opacity = '1';
            stockCard.style.opacity = '1';
        }
    }

    async function loadDashboardData() {
        showDashboardLoading(true);

        try {
            const response = await fetchWithAuth('/api/products');

            if (response.ok) {
                const data = await response.json();
                const products = data.data;

                document.getElementById('total-products').textContent = products.length;
                document.getElementById('total-stock').textContent =
                    products.reduce((sum, p) => sum + p.stock, 0);
            } else {
                console.error('Error loading dashboard data');
            }
        } catch (error) {
            console.error('Error loading data:', error);
        } finally {
            showDashboardLoading(false);
        }
    }

    // Load data on page load
    loadDashboardData();

    // Refresh dashboard data every 5 seconds for real-time updates
    dashboardRefreshInterval = setInterval(() => {
        loadDashboardData();
    }, 5000);
</script>
@endsection