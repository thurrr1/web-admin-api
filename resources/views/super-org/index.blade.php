@extends('layouts.app')

@section('title', 'Kelola Organisasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-dark fw-bold m-0"><i class="bi bi-building me-2"></i> Kelola Organisasi</h4>
        <a href="{{ route('super-org.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Organisasi
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Nama Organisasi</th>
                            <th class="py-3">Email Admin</th>
                            <th class="text-end pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organisasis as $org)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">#{{ $org['ID'] }}</td>
                                <td>
                                    <div class="fw-bold">{{ $org['nama_organisasi'] }}</div>
                                </td>
                                <td>{{ $org['email_admin'] ?? '-' }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAdmins({{ $org['ID'] }})">
                                            <i class="bi bi-people"></i> Daftar Admin
                                        </button>
                                        <a href="{{ route('super-org.create-admin', $org['ID']) }}" class="btn btn-sm btn-outline-info" title="Tambah Admin">
                                            <i class="bi bi-person-plus"></i> + Admin
                                        </a>
                                        <a href="{{ route('super-org.edit', $org['ID']) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr id="admins-row-{{ $org['ID'] }}" class="d-none bg-light">
                                <td colspan="4" class="p-3">
                                    <div id="admins-container-{{ $org['ID'] }}" class="p-2 border rounded bg-white">
                                        <div class="text-center text-muted py-2">Memuat data admin...</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data organisasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleAdmins(orgId) {
        const row = document.getElementById(`admins-row-${orgId}`);
        const container = document.getElementById(`admins-container-${orgId}`);
        
        if (row.classList.contains('d-none')) {
            row.classList.remove('d-none');
            fetchAdmins(orgId, container);
        } else {
            row.classList.add('d-none');
        }
    }

    function fetchAdmins(orgId, container) {
        container.innerHTML = '<div class="text-center text-muted py-2"><div class="spinner-border spinner-border-sm me-2"></div>Memuat...</div>';
        
        fetch(`/super-org/${orgId}/admins`)
            .then(response => response.json())
            .then(data => {
                if (!data.data || data.data.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted py-2">Belum ada admin untuk organisasi ini.</div>';
                    return;
                }
                
                let html = `
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIP</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                data.data.forEach(admin => {
                    const isChecked = admin.is_active ? 'checked' : '';
                    html += `
                        <tr>
                            <td>${admin.nip}</td>
                            <td>${admin.nama}</td>
                            <td>${admin.jabatan}</td>
                            <td>
                                <span class="badge ${admin.is_active ? 'bg-success' : 'bg-danger'}">
                                    ${admin.is_active ? 'Aktif' : 'Nonaktif'}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                        ${isChecked} onchange="toggleStatus(${admin.ID}, this, ${orgId})">
                                </div>
                            </td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table>';
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="text-center text-danger py-2">Gagal memuat data admin.</div>';
            });
    }

    function toggleStatus(adminId, checkbox, orgId) {
        const isActive = checkbox.checked;
        const originalState = !isActive;
        
        // Disable checkbox while processing
        checkbox.disabled = true;
        
        fetch(`/super-org/admin/${adminId}/toggle`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_active: isActive })
        })
        .then(response => response.json())
        .then(data => {
            checkbox.disabled = false;
            // Reload list to update badge status text
             const container = document.getElementById(`admins-container-${orgId}`);
             fetchAdmins(orgId, container);
        })
        .catch(error => {
            console.error('Error:', error);
            checkbox.checked = originalState; // Revert
            checkbox.disabled = false;
            alert('Gagal mengubah status admin.');
        });
    }
</script>
@endpush
