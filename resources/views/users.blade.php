@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User & Role')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Pengguna</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="users-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role Saat Ini</th>
                        <th>Ubah Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="users-tbody">
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

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-user-form">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-user-name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit-user-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-user-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-user-email" name="email" required>
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
    const roles = [{
            id: 1,
            name: 'Admin'
        },
        {
            id: 2,
            name: 'Seller'
        },
        {
            id: 3,
            name: 'Pelanggan'
        }
    ];

    let currentUser = null;

    async function getCurrentUser() {
        try {
            const response = await fetchWithAuth('/api/auth/me');
            if (response.ok) {
                const data = await response.json();
                currentUser = data.user;
                console.log('Current User:', currentUser);
            }
        } catch (error) {
            console.error('Error getting current user:', error);
        }
    }

    async function loadUsers() {
        try {
            const response = await fetchWithAuth('/api/users');

            if (response.ok) {
                const data = await response.json();
                const users = data.data;
                const tbody = document.getElementById('users-tbody');

                if (users.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada pengguna</td></tr>';
                    return;
                }

                tbody.innerHTML = users.map(user => {
                    const isCurrentUserAdmin = currentUser && currentUser.role && currentUser.role.name === 'Admin';
                    const isThisUserAdmin = user.role && user.role.name === 'Admin';
                    const canChangeRole = !(isCurrentUserAdmin && isThisUserAdmin);

                    console.log('User:', user.name, 'Admin:', isThisUserAdmin, 'Current User Admin:', isCurrentUserAdmin, 'Can Change:', canChangeRole);

                    return `
                    <tr>
                        <td><strong>${escapeHtml(user.name)}</strong></td>
                        <td>${escapeHtml(user.email)}</td>
                        <td>
                            <span class="badge badge-${user.role ? user.role.name.toLowerCase() : 'secondary'}">
                                ${user.role ? user.role.name : 'N/A'}
                            </span>
                        </td>
                        <td>
                            <select class="form-select form-select-sm" 
                                    onchange="changeRole(${user.id}, this.value)"
                                    id="role-select-${user.id}"
                                    ${!canChangeRole ? 'disabled' : ''}>
                                <option value="">-- Pilih Role --</option>
                                ${roles.map(role => `
                                    <option value="${role.id}" 
                                            ${user.role && user.role.id === role.id ? 'selected' : ''}>
                                        ${role.name}
                                    </option>
                                `).join('')}
                            </select>
                            ${!canChangeRole ? `<small class="text-muted d-block mt-1">Admin tidak dapat diubah</small>` : ''}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                ${canChangeRole ? `
                                <button type="button" class="btn btn-sm btn-success" 
                                        onclick="saveRole(${user.id})"
                                        id="save-btn-${user.id}">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                ` : ''}
                                <button type="button" class="btn btn-sm btn-info" 
                                        onclick="openEditUserModal(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.email)}')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                    `;
                }).join('');
            } else {
                document.getElementById('users-tbody').innerHTML =
                    '<tr><td colspan="5" class="text-center text-danger">Error loading users</td></tr>';
            }
        } catch (error) {
            console.error('Error loading users:', error);
            document.getElementById('users-tbody').innerHTML =
                '<tr><td colspan="5" class="text-center text-danger">Error: ' + error.message + '</td></tr>';
        }
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

    function changeRole(userId, roleId) {
        // Role selection changed - just store the selection
        document.getElementById(`save-btn-${userId}`).classList.add('btn-warning');
        document.getElementById(`save-btn-${userId}`).classList.remove('btn-success');
    }

    async function saveRole(userId) {
        const select = document.getElementById(`role-select-${userId}`);
        const roleId = select.value;

        if (!roleId) {
            showAlert('Pilih role terlebih dahulu', 'warning');
            return;
        }

        try {
            const response = await fetchWithAuth(`/api/users/${userId}/change-role`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    role_id: roleId
                })
            });

            if (response.ok) {
                document.getElementById(`save-btn-${userId}`).classList.remove('btn-warning');
                document.getElementById(`save-btn-${userId}`).classList.add('btn-success');
                showAlert('Role pengguna berhasil diubah', 'success');
                loadUsers();
            } else {
                const error = await response.json();
                showAlert(error.message || 'Error changing role', 'danger');
            }
        } catch (error) {
            showAlert('Error: ' + error.message, 'danger');
        }
    }

    function openEditUserModal(userId, name, email) {
        document.getElementById('edit-user-name').value = name;
        document.getElementById('edit-user-email').value = email;

        // Store ID for later use in form submission
        document.getElementById('edit-user-form').dataset.userId = userId;

        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }

    document.getElementById('edit-user-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const userId = this.dataset.userId;
        const formData = {
            name: document.getElementById('edit-user-name').value,
            email: document.getElementById('edit-user-email').value
        };

        try {
            const response = await fetchWithAuth(`/api/users/${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                showAlert('User berhasil diperbarui', 'success');
                loadUsers();
            } else {
                const error = await response.json();
                showAlert(error.message || 'Error updating user', 'danger');
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

    // Load current user first, then load users on page load
    getCurrentUser().then(() => {
        loadUsers();
    });
</script>
@endsection