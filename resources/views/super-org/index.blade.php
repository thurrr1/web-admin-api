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
                            <th class="ps-4 py-3" style="width: 5%;">ID</th>
                            <th class="py-3" style="width: 35%;">Nama Organisasi</th>
                            <th class="py-3" style="width: 30%;">Email Admin</th>
                            <th class="text-end pe-4 py-3" style="width: 30%;">Aksi</th>
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
                            <tr id="admins-row-{{ $org['ID'] }}" class="d-none">
                                <td colspan="4" class="p-0 border-0">
                                    <div id="admins-container-{{ $org['ID'] }}" class="bg-light border-bottom">
                                        <div class="text-center text-muted py-3">Memuat data admin...</div>
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
                    <div class="ms-auto" style="width: 95%;">
                        <table class="table table-sm mb-0 table-borderless" style="table-layout: fixed;">
                            <thead class="text-secondary" style="font-size: 0.9rem;">
                                <tr>
                                    <th class="border-top-0" style="width: 25%">NIP</th>
                                    <th class="border-top-0" style="width: 35%">Nama</th>
                                    <th class="border-top-0" style="width: 25%">Jabatan</th>
                                    <th class="border-top-0" style="width: 15%">Status</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                `;
                
                data.data.forEach(admin => {
                    const adminId = admin.ID || admin.id; // Handle casing ID
                    const isChecked = admin.is_active ? 'checked' : '';
                    const statusLabel = admin.is_active ? 'Aktif' : 'Nonaktif';
                    const statusClass = admin.is_active ? 'text-success' : 'text-muted';
                    
                    html += `
                        <tr>
                            <td class="text-muted">${admin.nip}</td>
                            <td class="text-muted">${admin.nama}</td>
                            <td class="text-muted">${admin.jabatan}</td>
                            <td>
                                <div class="form-check form-switch" style="transform: scale(0.8); transform-origin: left center;">
                                    <input class="form-check-input" type="checkbox" role="switch" 
                                        id="status-switch-${adminId}"
                                        ${isChecked} onchange="toggleStatus(${adminId}, this, ${orgId})">
                                    <label class="form-check-label ${statusClass} fw-bold" for="status-switch-${adminId}" style="font-size: 1.1rem;">
                                        ${statusLabel}
                                    </label>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="text-center text-danger py-2">Gagal memuat data admin.</div>';
            });
    }

    function toggleStatus(adminId, checkbox, orgId) {
        if (!adminId) {
            console.error('Invalid Admin ID');
            alert('Terjadi kesalahan sistem: ID Admin tidak valid.');
            checkbox.checked = !checkbox.checked;
            return;
        }

        const isActive = checkbox.checked;
        const originalState = !isActive;
        
        // Disable checkbox while processing
        checkbox.disabled = true;
        
        // Optimistic UI update
        const label = document.querySelector(`label[for="status-switch-${adminId}"]`);
        if(label) {
             label.textContent = isActive ? 'Aktif' : 'Nonaktif';
             label.className = `form-check-label fw-bold ${isActive ? 'text-success' : 'text-muted'}`;
        }

        try {
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenMeta) {
                throw new Error('CSRF Token meta tag not found');
            }
            const csrfToken = csrfTokenMeta.getAttribute('content');

            fetch(`/super-org/admin/${adminId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ is_active: isActive })
            })
            .then(async response => {
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Server responded with ${response.status}: ${errorText}`);
                }
                return response.json();
            })
            .then(data => {
                checkbox.disabled = false;
                console.log('Status updated successfully');
            })
            .catch(error => {
                console.error('Error in toggleStatus:', error);
                // Revert UI
                checkbox.checked = originalState; 
                checkbox.disabled = false;
                if(label) {
                    label.textContent = originalState ? 'Aktif' : 'Nonaktif';
                    label.className = `form-check-label fw-bold ${originalState ? 'text-success' : 'text-muted'}`;
                }
                alert('Gagal mengubah status admin: ' + (error.message || 'Terjadi kesalahan server'));
            });
        } catch (e) {
            console.error('Synchronous Error:', e);
            // Revert UI if sync error
            checkbox.checked = originalState;
            checkbox.disabled = false;
             if(label) {
                label.textContent = originalState ? 'Aktif' : 'Nonaktif';
                label.className = `form-check-label fw-bold ${originalState ? 'text-success' : 'text-muted'}`;
            }
            alert('Gagal memproses permintaan: ' + e.message);
        }
    }
</script>
@endpush
