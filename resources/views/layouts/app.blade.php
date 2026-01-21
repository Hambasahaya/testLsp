<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            display: flex;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .sidebar h3 {
            margin-bottom: 30px;
            font-weight: 700;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 15px;
        }

        .sidebar a {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .sidebar a.active {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 600;
        }

        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 20px;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border-radius: 10px;
        }

        .navbar-brand {
            color: #667eea !important;
            font-weight: 700;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px 10px 0 0;
            padding: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8a 100%);
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }

        .badge-admin {
            background: #667eea;
        }

        .badge-seller {
            background: #fbbf24;
        }

        .badge-customer {
            background: #10b981;
        }

        .user-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 30px;
            border-top: 2px solid rgba(255, 255, 255, 0.2);
        }

        .user-info small {
            display: block;
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="sidebar">
        <h3><i class="fas fa-boxes"></i> Inventory</h3>

        <a href="{{ route('dashboard') }}" class="@if(Route::currentRouteName() == 'dashboard') active @endif">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="{{ route('inventory') }}" class="@if(Route::currentRouteName() == 'inventory') active @endif">
            <i class="fas fa-cubes"></i> Inventaris
        </a>

        @if(auth()->user()->hasRole('Admin'))
        <a href="{{ route('users') }}" class="@if(Route::currentRouteName() == 'users') active @endif">
            <i class="fas fa-users"></i> Manajemen User
        </a>
        @endif

        <div class="user-info">
            <small><strong>Logged in as:</strong></small>
            <small>{{ auth()->user()->name }}</small>
            <small>
                <span class="badge badge-{{ strtolower(auth()->user()->role->name) }}">
                    {{ auth()->user()->role->name }}
                </span>
            </small>
            <form action="{{ route('web.logout') }}" method="POST" style="margin-top: 10px;">
                @csrf
                <button type="submit" class="btn btn-sm btn-light w-100">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <nav class="navbar">
            <div class="container-fluid">
                <span class="navbar-brand">@yield('page-title', 'Dashboard')</span>
            </div>
        </nav>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Setup fetch to always include session cookies and CSRF token
        window.fetchWithAuth = function(url, options = {}) {
            const defaultOptions = {
                credentials: 'include', // Include cookies for session auth
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            };

            return fetch(url, {
                ...defaultOptions,
                ...options,
                headers: {
                    ...defaultOptions.headers,
                    ...(options.headers || {})
                }
            });
        };
    </script>
    @yield('extra-js')
</body>

</html>